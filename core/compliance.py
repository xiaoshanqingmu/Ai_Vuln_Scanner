import logging
import fnmatch
from typing import Tuple

from flask import has_request_context, request

from config.settings import AUTHORIZED_DOMAINS, STRICT_COMPLIANCE_MODE

# 使用独立的合规模块日志记录器
logger = logging.getLogger("compliance")

# 简单的内存映射，用于记录用户是否完成授权确认
# 生产环境可扩展为数据库表记录
_USER_CONFIRM_MAP = {}


def _get_operator() -> str:
    """
    获取当前操作人标识。

    优先使用 Flask 请求中的远端地址，若不可用则返回 anonymous。
    """
    if has_request_context():
        return request.remote_addr or "anonymous"
    return "anonymous"


def _normalize_target(domain: str) -> str:
    """
    规范化目标地址字符串。

    仅做去除前后空格、小写处理，保留端口等信息，便于白名单精确匹配。

    :param domain: 原始目标地址（域名/IP，可携带端口）
    :return: 规范化后的目标字符串
    """
    return (domain or "").strip().lower()


def _match_authorized_pattern(target: str, pattern: str) -> bool:
    """
    匹配目标与白名单模式。

    支持以下形式：
    - 精确域名或 IP（如 buuctf.cn、1.1.1.1）
    - 携带端口的地址（如 buuctf.cn:8080、1.1.1.1:8080）
    - 通配符前缀（如 *.buuctf.cn、*.buuoj.cn），带端口的目标会先用“主机”部分参与匹配

    :param target: 规范化后的目标地址（可含端口）
    :param pattern: 白名单中的单条模式
    :return: 是否匹配
    """
    target = _normalize_target(target)
    pattern = _normalize_target(pattern)

    if not target or not pattern:
        return False

    # 先整串匹配（如 pattern 为 example.com:8080）
    if fnmatch.fnmatch(target, pattern):
        return True
    # 若 pattern 不含端口，则用目标的“主机”部分再匹配一次，便于 *.buuoj.cn 匹配 xxx.buuoj.cn:81
    if ":" not in pattern and ":" in target:
        target_host = target.split(":", 1)[0]
        if fnmatch.fnmatch(target_host, pattern):
            return True
    return False


def check_authorized_domain(domain: str) -> Tuple[bool, str]:
    """
    校验目标是否在授权白名单中。

    在严格模式下，只有匹配白名单的目标才允许继续执行扫描任务。

    :param domain: 目标地址（公网域名/IP，可携带端口）
    :return: (是否授权, 说明信息)
    """
    try:
        target = _normalize_target(domain)
        if not target:
            logger.warning("合规校验失败：目标地址为空")
            return False, "目标地址不能为空"

        if not AUTHORIZED_DOMAINS:
            # 未配置白名单时，允许所有公网靶场
            logger.info("未配置白名单，允许扫描: target=%s", target)
            return True, "允许扫描所有公网靶场"

        for pattern in AUTHORIZED_DOMAINS:
            if _match_authorized_pattern(target, pattern):
                logger.info("目标通过白名单校验: target=%s pattern=%s", target, pattern)
                return True, "目标在授权白名单中"

        logger.warning("目标未通过白名单校验: target=%s whitelist=%s", target, AUTHORIZED_DOMAINS)
        return False, "目标不在授权白名单中，请确认授权范围"
    except Exception as exc:  # noqa: BLE001
        logger.exception("白名单解析或匹配失败: domain=%s 错误=%s", domain, exc)
        return False, "白名单解析失败，请联系管理员检查配置"


def record_scan_audit(task_id: str, domain: str, user_confirmed: bool) -> None:
    """
    记录扫描合规审计日志。

    日志内容包括时间、任务 ID、目标地址、操作人、授权确认结果等。
    同时在内存中记录用户是否已确认授权，用于后续校验。

    :param task_id: 扫描任务 ID
    :param domain: 扫描目标（公网域名/IP，可携带端口）
    :param user_confirmed: 用户是否在前端完成授权确认
    """
    try:
        operator = _get_operator()
        target = _normalize_target(domain)
        _USER_CONFIRM_MAP[task_id] = bool(user_confirmed)

        status = "confirmed" if user_confirmed else "not_confirmed"
        logger.info(
            "扫描合规审计: task_id=%s target=%s operator=%s user_confirmed=%s status=%s",
            task_id,
            target,
            operator,
            user_confirmed,
            status,
        )
    except Exception as exc:  # noqa: BLE001
        # 审计日志写入失败不应影响主流程，但需要记录错误
        logger.exception(
            "记录扫描合规审计日志失败: task_id=%s domain=%s 错误=%s",
            task_id,
            domain,
            exc,
        )


def verify_user_confirm(task_id: str) -> bool:
    """
    验证用户是否完成授权确认。

    在严格模式下，若用户未确认授权，则不允许继续执行扫描任务。
    在非严格模式下，即使未确认授权也允许继续（但仍建议记录审计日志）。

    :param task_id: 扫描任务 ID
    :return: 是否通过授权确认校验
    """
    try:
        confirmed = _USER_CONFIRM_MAP.get(task_id, False)
        if STRICT_COMPLIANCE_MODE and not confirmed:
            logger.warning(
                "严格模式下授权确认未通过: task_id=%s confirmed=%s",
                task_id,
                confirmed,
            )
            return False

        logger.info(
            "授权确认校验通过: task_id=%s confirmed=%s strict=%s",
            task_id,
            confirmed,
            STRICT_COMPLIANCE_MODE,
        )
        return True
    except Exception as exc:  # noqa: BLE001
        logger.exception("授权确认校验异常: task_id=%s 错误=%s", task_id, exc)
        # 为安全起见，在严格模式下遇到异常一律视为未通过
        if STRICT_COMPLIANCE_MODE:
            return False
        return True


__all__ = [
    "check_authorized_domain",
    "record_scan_audit",
    "verify_user_confirm",
]

