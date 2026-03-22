import logging
import time
from typing import Any, Dict, List, Optional, Tuple

import requests

from config.settings import QWEN_API_KEY, QWEN_API_URL, QWEN_MODEL, REQUEST_DELAY
from database.db_operation import (
    get_vulnerabilities_by_task_id,
    update_vulnerability_ai_info,
)
from database.models import Vulnerability

logger = logging.getLogger("ai")

# 简单的进程级调用速率控制（每秒最多 1 次）
_LAST_CALL_TS: float = 0.0


def _rate_limit() -> None:
    """
    简单的 QWEN API 调用速率限制。

    确保两次调用间隔至少 1 秒，避免触发频率限制。
    """
    global _LAST_CALL_TS  # noqa: PLW0603
    now = time.time()
    delta = now - _LAST_CALL_TS
    if delta < 1.0:
        time.sleep(1.0 - delta)
    _LAST_CALL_TS = time.time()


def _detect_vuln_type(vuln: Vulnerability) -> str:
    """
    根据漏洞名称与描述粗略识别漏洞类型。

    :param vuln: 漏洞模型实例
    :return: 漏洞类型字符串（如 sql_injection / xss / ssrf / file_upload / default）
    """
    name = (vuln.vuln_name or "").lower()
    desc = (vuln.description or "").lower()
    text = name + " " + desc

    if any(k in text for k in ["sql injection", "sqli", "注入", "sql 注入"]):
        return "sql_injection"
    if any(k in text for k in ["xss", "cross-site scripting", "跨站脚本"]):
        return "xss"
    if any(k in text for k in ["ssrf", "server-side request forgery", "服务端请求伪造"]):
        return "ssrf"
    if any(k in text for k in ["file upload", "upload", "文件上传"]):
        return "file_upload"
    return "default"


def _build_prompt_for_vuln(vuln: Vulnerability) -> str:
    """
    针对单个漏洞构造通义千问提示词。

    不返回完整 PoC 代码，仅提供 PoC 思路与修复建议，避免合规风险。
    """
    vuln_type = _detect_vuln_type(vuln)
    base_context = (
        "你是一名资深渗透测试工程师兼安全架构师，需要针对公网授权靶场环境中的漏洞进行深度分析。\n"
        "请使用专业、安全、合规的语言进行描述，避免输出任何可直接复制执行的完整攻击代码，仅提供 PoC 构造思路。\n"
        "目标环境为授权靶场（例如 BUUCTF 等），所有分析仅用于教学与防御建设。\n\n"
    )

    common_info = (
        f"漏洞名称：{vuln.vuln_name}\n"
        f"风险等级：{vuln.risk_level}\n"
        f"漏洞 URL：{vuln.url}\n"
        f"参数名称：{vuln.param or '（未识别）'}\n"
        f"扫描工具：{vuln.tool}\n"
        f"工具描述：{vuln.description or '（无描述）'}\n"
    )

    if vuln_type == "sql_injection":
        task = (
            "请从以下三个维度对该 SQL 注入相关漏洞进行分析：\n"
            "1. 攻击原理：说明 SQL 注入产生的根本原因、典型利用方式，以及在授权靶场中的常见利用路径；\n"
            "2. PoC 思路：给出构造注入 payload 的思路和步骤（例如如何区分数字型/字符型注入、如何利用报错/盲注等），"
            "但不要给出可直接复制执行的完整 payload；\n"
            "3. 定制化修复建议：结合上述 URL 和参数名称，例如“针对 {url} 的 {param} 参数”，"
            "给出基于预编译语句、输入校验、最小权限等多层防护建议。\n"
        ).format(url=vuln.url, param=vuln.param or "相关")
    elif vuln_type == "xss":
        task = (
            "请从以下三个维度对该 XSS 相关漏洞进行分析：\n"
            "1. 攻击原理：说明反射型/存储型/DOM 型 XSS 的产生原因及差异；\n"
            "2. PoC 思路：给出构造 XSS payload 的思路（如如何测试输出点、如何识别过滤规则），"
            "但不要给出完整可执行的 JS 代码；\n"
            "3. 定制化修复建议：结合上述 URL 和参数名称，例如“针对 {url} 的 {param} 参数”，"
            "说明输出编码、白名单校验、内容安全策略（CSP）等防护措施。\n"
        ).format(url=vuln.url, param=vuln.param or "相关")
    elif vuln_type == "ssrf":
        task = (
            "请从以下三个维度对该 SSRF 相关漏洞进行分析：\n"
            "1. 攻击原理：说明 SSRF 如何利用服务器对外/对内发起请求的能力，以及在靶场中的典型利用目标；\n"
            "2. PoC 思路：给出探测内网/云元数据服务的思路，但不要给出具体内网地址或可直接利用的 URL；\n"
            "3. 定制化修复建议：结合上述 URL 和参数名称，例如“针对 {url} 的 {param} 参数”，"
            "说明如何做协议/地址白名单、DNS 解析限制、请求超时和重定向控制。\n"
        ).format(url=vuln.url, param=vuln.param or "相关")
    elif vuln_type == "file_upload":
        task = (
            "请从以下三个维度对该文件上传相关漏洞进行分析：\n"
            "1. 攻击原理：说明任意文件上传、类型绕过、路径穿越等风险；\n"
            "2. PoC 思路：给出上传恶意文件的一般思路（如扩展名绕过、MIME 绕过），"
            "但不要提供完整的木马脚本内容；\n"
            "3. 定制化修复建议：结合上述 URL 和参数名称，例如“针对 {url} 的上传接口”，"
            "说明文件类型白名单、内容检测、存储路径隔离等措施。\n"
        ).format(url=vuln.url)
    else:
        task = (
            "请根据上述信息，从攻击原理、PoC 思路（不包含完整可执行代码）和防御建议三个角度，"
            "对该漏洞给出简明但有深度的分析，并尽量结合 URL 与参数名称给出定制化修复建议。\n"
        )

    return base_context + common_info + "\n" + task


def _build_prompt_for_attack_chain(task_id: str, vulns: List[Vulnerability]) -> str:
    """
    构造针对攻击链分析的提示词。
    """
    base = (
        "你是一名资深渗透测试工程师兼红队负责人，需要对一次授权靶场的综合渗透测试结果进行攻击链分析。\n"
        "请基于给定的多个漏洞条目，推演可能的攻击路径和攻击链条，并给出整体风险评估与修复优先级建议。\n"
        "注意：仅输出分析与防御建议，不给出任何完整利用代码或可复制执行的命令。\n\n"
    )

    detail_lines = []
    for idx, v in enumerate(vulns, start=1):
        detail_lines.append(
            f"{idx}. [{v.risk_level}] {v.vuln_name} - URL: {v.url} - 参数: {v.param or '（未识别）'} - 工具: {v.tool}"
        )

    detail = "\n".join(detail_lines) if detail_lines else "（当前任务下未找到漏洞记录）"

    tail = (
        "\n\n请输出内容包括：\n"
        "1. 可能的攻击链条示意（例如：信息收集 -> 弱口令 -> 文件上传 -> RCE 等）；\n"
        "2. 该攻击链在真实业务中的潜在影响（机密性/完整性/可用性）；\n"
        "3. 针对本次任务（task_id={task_id}）的整改优先级与分阶段修复建议。\n"
    ).format(task_id=task_id)

    return base + "漏洞列表：\n" + detail + tail


def _call_qwen_api(prompt: str, timeout: int = 30) -> Tuple[bool, str]:
    """
    调用通义千问 API 的通用封装。

    :param prompt: 完整提示词
    :param timeout: 请求超时时间（秒）
    :return: (是否成功, 返回文本或错误信息)
    """
    if not QWEN_API_KEY or not QWEN_API_URL:
        logger.warning("QWEN_API_KEY 或 QWEN_API_URL 未配置，将使用降级方案")
        return False, "AI 服务未配置，使用通用说明。"

    headers = {
        "Content-Type": "application/json",
        "Authorization": f"Bearer {QWEN_API_KEY}",
    }
    payload: Dict[str, Any] = {
        # DashScope 该接口的必填字段
        "model": QWEN_MODEL,
        "input": {
            "prompt": prompt,
        },
        # 具体字段结构可根据实际 API 文档微调
        "parameters": {
            "max_tokens": 800,
            "temperature": 0.7,
        },
    }

    try:
        _rate_limit()
        logger.info("调用通义千问 API 开始")
        resp = requests.post(QWEN_API_URL, json=payload, headers=headers, timeout=timeout)
        if resp.status_code == 401:
            logger.error("通义千问认证失败（可能 API Key 错误或额度不足）：%s", resp.text)
            return False, "AI 服务认证失败或额度不足。"
        if resp.status_code >= 500:
            logger.error("通义千问服务端错误：%s", resp.text)
            return False, "AI 服务暂时不可用，请稍后重试。"
        if resp.status_code != 200:
            logger.error("通义千问返回非 200 状态码：%s %s", resp.status_code, resp.text)
            return False, "AI 服务调用失败，请稍后重试。"

        data = resp.json()
        # 不同版本接口字段可能不同，这里做一个兼容性解析
        output_text = (
            data.get("output", {}).get("text")
            or data.get("result", {}).get("output_text")
            or ""
        )
        if not output_text:
            logger.warning("通义千问返回结果解析失败：%s", data)
            return False, "AI 服务返回格式异常。"

        logger.info("调用通义千问 API 成功")
        return True, str(output_text)
    except requests.Timeout:
        logger.error("通义千问调用超时（> %s 秒）", timeout)
        return False, "AI 服务调用超时，请稍后重试。"
    except requests.RequestException as exc:
        logger.error("通义千问网络请求异常：%s", exc)
        return False, "AI 服务网络异常，请检查网络环境。"
    except ValueError as exc:
        logger.error("通义千问返回解析 JSON 失败：%s", exc)
        return False, "AI 服务返回解析失败。"
    except Exception as exc:  # noqa: BLE001
        logger.exception("通义千问调用未知异常：%s", exc)
        return False, "AI 服务出现未知异常。"


def _fallback_analysis(vuln: Vulnerability) -> Tuple[str, str, str]:
    """
    当 AI 服务不可用时的降级方案。

    返回基于已有漏洞信息的通用说明，不影响报告生成。
    """
    attack_principle = (
        f"根据现有信息（漏洞名称：{vuln.vuln_name}，风险等级：{vuln.risk_level}），"
        "该漏洞可能导致未授权访问、敏感信息泄露或权限提升。"
    )
    poc_suggestion = (
        "由于当前 AI 服务不可用，仅提供 PoC 思路："
        "可以根据漏洞类型构造边界值和恶意输入，对相关参数进行测试，"
        "观察响应差异以确认漏洞是否可被稳定利用。"
    )
    custom_fix = (
        f"请针对 URL {vuln.url} 中的参数 {vuln.param or '相关参数'}，"
        "结合业务逻辑增加输入校验、最小权限控制和安全审计日志，"
        "并参考通用安全加固基线进行整体修复。"
    )
    return attack_principle, poc_suggestion, custom_fix


def analyze_vulnerability(vuln_id: int, vuln_data: Optional[Vulnerability] = None) -> Tuple[bool, str]:
    """
    针对单个漏洞执行 AI 深度分析。

    - 若数据库中已存在 AI 字段内容，则直接返回并视为缓存命中；
    - 否则调用通义千问生成攻击原理、PoC 建议和定制化修复建议，并写回数据库。

    :param vuln_id: 漏洞记录主键 ID
    :param vuln_data: 可选的 Vulnerability 实例，若未提供将从数据库查询
    :return: (是否成功, 提示信息)
    """
    from database.models import Vulnerability as VulnModel  # 避免循环导入
    from app import db  # noqa: WPS433

    with db.session.no_autoflush:
        vuln: Optional[VulnModel]
        if vuln_data is not None:
            vuln = vuln_data
        else:
            vuln = VulnModel.query.get(vuln_id)

        if not vuln:
            logger.warning("AI 分析失败，漏洞记录不存在: id=%s", vuln_id)
            return False, "漏洞记录不存在。"

        # 简单缓存：若已有 AI 字段，则视为已分析过
        if vuln.attack_principle and vuln.poc_suggestion and vuln.custom_fix:
            logger.info("AI 分析缓存命中: id=%s url=%s", vuln.id, vuln.url)
            return True, "已使用缓存的 AI 分析结果。"

        prompt = _build_prompt_for_vuln(vuln)
        ok, output_text = _call_qwen_api(prompt)

        if ok:
            # 这里简单将整段输出同时填入三个字段，实际可按段落拆分
            attack_principle = output_text
            poc_suggestion = "基于上述分析内容，提炼 PoC 构造思路（不含完整代码）。"
            custom_fix = "基于上述分析内容，提炼定制化修复建议。"
        else:
            attack_principle, poc_suggestion, custom_fix = _fallback_analysis(vuln)

        success, _, _ = update_vulnerability_ai_info(
            vulnerability_id=vuln.id,
            attack_principle=attack_principle,
            poc_suggestion=poc_suggestion,
            custom_fix=custom_fix,
        )
        if success:
            logger.info("AI 分析结果已写入漏洞记录: id=%s", vuln.id)
            return True, "AI 分析完成并已写入数据库。"
        logger.error("AI 分析结果写入数据库失败: id=%s", vuln.id)
        return False, "AI 分析结果写入数据库失败。"


def analyze_attack_chain(task_id: str) -> Tuple[bool, str]:
    """
    针对某个任务下的全部漏洞，生成攻击链风险评估。

    结果写入每条漏洞记录的 attack_chain_risk 字段（保持一致的整体结论），
    以便在报告生成阶段统一引用。

    :param task_id: 扫描任务 ID（对应 Vulnerability.task_id）
    :return: (是否成功, 提示信息)
    """
    vulns = get_vulnerabilities_by_task_id(task_id)
    if not vulns:
        logger.info("攻击链分析：任务下无漏洞记录: task_id=%s", task_id)
        return True, "该任务下未发现漏洞，无需攻击链分析。"

    prompt = _build_prompt_for_attack_chain(task_id, vulns)
    ok, output_text = _call_qwen_api(prompt)

    if not ok:
        # 降级：给出通用攻击链说明
        output_text = (
            "根据当前任务下的漏洞分布，可以构成从信息收集、权限获取到横向移动的攻击链。"
            "建议优先修复高危和可直接导致数据泄露/命令执行的漏洞，并对关键业务路径增加监控与审计。"
        )

    # 将攻击链结论写入所有该任务下的漏洞记录
    count = 0
    for v in vulns:
        success, _, _ = update_vulnerability_ai_info(
            vulnerability_id=v.id,
            attack_chain_risk=output_text,
        )
        if success:
            count += 1

    logger.info("攻击链分析完成: task_id=%s 更新条数=%s", task_id, count)
    return True, f"攻击链分析完成，已更新 {count} 条漏洞记录。"


__all__ = [
    "analyze_vulnerability",
    "analyze_attack_chain",
]

