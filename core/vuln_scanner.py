import json
import logging
import glob
import os
import random
import subprocess
import tempfile
import time
from dataclasses import dataclass
from typing import Any, Dict, Iterable, List, Optional, Tuple
from urllib.parse import urlparse

from zapv2 import ZAPv2

from config.settings import (
    NUCLEI_PATH,
    NUCLEI_TEMPLATES_DIR,
    NUCLEI_TARGETED_TEMPLATES,
    DNSLOG_BASE_URL,
    DNSLOG_COOKIE,
    PROXY_LIST,
    REQUEST_DELAY,
    STRICT_COMPLIANCE_MODE,
    USER_AGENT_POOL,
    ZAP_API_KEY,
    ZAP_API_URL,
    ZAP_SCAN_TIMEOUT,
)
from database.db_operation import add_vulnerability
from core.dnslog_client import DNSLogClient
from .compliance import check_authorized_domain

logger = logging.getLogger("scan")


def _random_user_agent() -> str:
    """
    获取随机 User-Agent。

    优先使用 fake-useragent（若可用），否则回退到配置中的 USER_AGENT_POOL。
    """
    try:
        from fake_useragent import UserAgent  # noqa: WPS433

        ua = UserAgent()
        return ua.random
    except Exception:  # noqa: BLE001
        if USER_AGENT_POOL:
            return random.choice(USER_AGENT_POOL)
        return (
            "Mozilla/5.0 (Windows NT 10.0; Win64; x64) "
            "AppleWebKit/537.36 (KHTML, like Gecko) "
            "Chrome/120.0.0.0 Safari/537.36"
        )


def _pick_proxy() -> Optional[str]:
    """
    从代理列表中随机选择一个代理。

    :return: 代理字符串（例如 http://127.0.0.1:8080）或 None
    """
    proxies = [p for p in PROXY_LIST if str(p).strip()]
    if not proxies:
        return None
    return random.choice(proxies)


def _risk_level_normalize(level: str) -> str:
    """
    风险等级归一化（兼容 ZAP / nuclei）。

    nuclei: critical -> high（按需求保持与 ZAP 一致）
    :param level: 原始风险等级
    :return: 归一化后的风险等级（小写）
    """
    if not level:
        return "info"
    lv = str(level).strip().lower()
    if lv == "critical":
        return "high"
    if lv in {"high", "medium", "low", "info"}:
        return lv
    # ZAP 可能返回 Informational 等
    if "info" in lv:
        return "info"
    return "info"


def _extract_domain_from_url(url: str) -> str:
    """
    从 URL 中提取域名/IP + 端口（若存在）。

    :param url: 完整 URL
    :return: 例如 example.com:8080 或 example.com
    """
    parsed = urlparse(url)
    if not parsed.hostname:
        return ""
    if parsed.port:
        return f"{parsed.hostname}:{parsed.port}"
    return parsed.hostname


@dataclass
class VulnItem:
    """
    扫描结果的统一数据结构。
    """

    tool: str
    vuln_name: str
    risk_level: str
    url: str
    param: Optional[str] = None
    description: Optional[str] = None


class ZapScanner:
    """
    OWASP ZAP 扫描器封装。

    依赖 ZAP 以 Daemon/GUI 方式启动并开启 API。
    """

    def __init__(
        self,
        api_url: str = "http://127.0.0.1:8080",
        api_key: str = "",
        timeout: int = 600,
        delay_in_ms: int = 1000,
    ) -> None:
        """
        :param api_url: ZAP API 地址（默认本机 8080）
        :param api_key: ZAP API Key（若开启）
        :param timeout: ZAP API 操作超时时间（秒）
        :param delay_in_ms: ZAP 请求延迟（毫秒），用于公网适配
        """
        self.timeout = timeout
        # zapv2 通过 "proxies" 指向 ZAP API 入口（不是上游代理）
        proxies = {"http": api_url, "https": api_url}
        self.zap = ZAPv2(apikey=api_key, proxies=proxies)
        self.api_url = api_url
        self.delay_in_ms = delay_in_ms
        self.api_key = api_key

    def _dvwa_prepare_session_cookie(self, base_url: str) -> Tuple[bool, str]:
        """
        DVWA 专用：登录并设置 security=low，返回可复用的 Cookie header 字符串。
        """
        try:
            import re

            import requests

            s = requests.Session()
            r = s.get(f"{base_url}/login.php", timeout=10)
            if r.status_code >= 400:
                return False, "DVWA login.php 访问失败"

            # dvwa 的 CSRF token
            m = re.search(r"name=['\"]user_token['\"]\\s+value=['\"]([^'\"]+)['\"]", r.text or "")
            token = m.group(1) if m else ""

            resp = s.post(
                f"{base_url}/login.php",
                data={
                    "username": "admin",
                    "password": "password",
                    "Login": "Login",
                    "user_token": token,
                },
                allow_redirects=True,
                timeout=10,
            )
            if resp.status_code >= 400:
                return False, "DVWA 登录失败（HTTP 错误）"

            # 设置 security=low（需要新 token）
            sec = s.get(f"{base_url}/security.php", timeout=10)
            m2 = re.search(r"name=['\"]user_token['\"]\\s+value=['\"]([^'\"]+)['\"]", sec.text or "")
            token2 = m2.group(1) if m2 else ""
            _ = s.post(
                f"{base_url}/security.php",
                data={"security": "low", "seclev_submit": "Submit", "user_token": token2},
                allow_redirects=True,
                timeout=10,
            )

            ck = s.cookies.get_dict()
            if not ck:
                return False, "DVWA 未获得会话 Cookie"
            cookie_header = "; ".join([f"{k}={v}" for k, v in ck.items()])
            return True, cookie_header
        except Exception as exc:  # noqa: BLE001
            logger.warning("DVWA 会话准备失败: %s", exc)
            return False, "DVWA 会话准备失败"

    def _zap_set_cookie_replacer(self, cookie_header: str) -> None:
        """
        给 ZAP 加一条 replacer 规则，强制携带 Cookie（用于已登录会话）。
        """
        try:
            # 先尽量删除同名规则，避免重复叠加
            try:
                rules = self.zap.replacer.rules
                for r in rules or []:
                    if str(r.get("description") or "") == "dvwa-cookie":
                        self.zap.replacer.remove_rule(r.get("id"))
            except Exception:  # noqa: BLE001
                pass

            self.zap.replacer.add_rule(
                description="dvwa-cookie",
                enabled="true",
                matchtype="REQ_HEADER",
                matchregex="false",
                matchstring="Cookie",
                replacement=cookie_header,
            )
        except Exception as exc:  # noqa: BLE001
            logger.warning("ZAP 设置 Cookie replacer 失败（将继续执行）: %s", exc)

    def _ensure_zap_ready(self) -> Tuple[bool, str]:
        """
        检查 ZAP 服务是否可用。
        """
        try:
            _ = self.zap.core.version
            return True, "ZAP 服务可用"
        except Exception as exc:  # noqa: BLE001
            logger.exception("ZAP 服务不可用: %s", exc)
            return False, "ZAP 服务未启动或 API 不可用"

    def scan_urls(self, urls: List[str]) -> Tuple[bool, List[VulnItem], str]:
        """
        使用 ZAP 对 URL 列表进行扫描并解析结果。

        扫描策略（轻量实现）：
        - 设置全局 User-Agent（随机）
        - 设置延迟 delayInMs=1000
        - 逐个访问 URL 并执行 active scan
        - 汇总 alerts
        """
        ok, msg = self._ensure_zap_ready()
        if not ok:
            # 将 ZAP 未启动视为“功能降级”，记录日志但不让整个任务失败
            logger.warning("ZAP 未就绪，将跳过 ZAP 扫描: %s", msg)
            return True, [], msg

        started = time.time()
        ua = _random_user_agent()
        logger.info("ZAP 扫描开始: urls=%s ua=%s delay_ms=%s", len(urls), ua, self.delay_in_ms)

        try:
            # 设置 User-Agent 与延迟（部分选项可能因 ZAP 版本不同而失败，因此容错）
            try:
                self.zap.core.set_option_default_user_agent(ua)
            except Exception:  # noqa: BLE001
                logger.warning("ZAP 设置默认 User-Agent 失败，继续执行")
            try:
                self.zap.ascan.set_option_delay_in_ms(str(self.delay_in_ms))
            except Exception:  # noqa: BLE001
                logger.warning("ZAP 设置 delayInMs 失败，继续执行")

            # DVWA：若目标站点包含 /login.php 且可访问，则自动登录并强制带 Cookie，提高覆盖率
            try:
                base = urls[0].split("?", 1)[0].rstrip("/")
                parsed = urlparse(base)
                base_url = f"{parsed.scheme}://{parsed.netloc}"
                ok_ck, cookie_header = self._dvwa_prepare_session_cookie(base_url)
                if ok_ck and cookie_header:
                    self._zap_set_cookie_replacer(cookie_header)
                    logger.info("DVWA 会话已准备，ZAP 将携带 Cookie 扫描: %s", base_url)
            except Exception:  # noqa: BLE001
                pass

            # 先 spider / ajax spider（覆盖更多路径），再 active scan
            seed = urls[0]
            try:
                spider_id = self.zap.spider.scan(seed)
                while True:
                    status = int(self.zap.spider.status(spider_id))
                    if status >= 100:
                        break
                    time.sleep(2)
                    if time.time() - started > self.timeout:
                        raise TimeoutError("ZAP spider 超时")
            except Exception as exc:  # noqa: BLE001
                logger.warning("ZAP spider 失败（将继续）：%s", exc)

            try:
                _ = self.zap.ajaxSpider.scan(seed)
                # ajaxSpider 没有百分比状态，用固定时间窗口轮询
                deadline = time.time() + min(120, max(20, int(self.timeout / 5)))
                while time.time() < deadline:
                    status = str(self.zap.ajaxSpider.status).lower()
                    if status in {"stopped", "complete"}:
                        break
                    time.sleep(2)
            except Exception as exc:  # noqa: BLE001
                logger.warning("ZAP ajax spider 失败（将继续）：%s", exc)

            # 逐个 URL 做访问与主动扫描（避免一次性对公网压力过大）
            for url in urls:
                time.sleep(max(0.0, float(REQUEST_DELAY)))
                try:
                    self.zap.core.access_url(url)
                except Exception:  # noqa: BLE001
                    logger.debug("ZAP access_url 失败（可能被 WAF/网络波动）：%s", url)

                try:
                    scan_id = self.zap.ascan.scan(url)
                    # 等待扫描完成
                    while True:
                        status = int(self.zap.ascan.status(scan_id))
                        if status >= 100:
                            break
                        time.sleep(2)
                        # 超时控制
                        if time.time() - started > self.timeout:
                            raise TimeoutError("ZAP 扫描超时")
                except TimeoutError:
                    logger.warning("ZAP 扫描超时，停止后续扫描")
                    break
                except Exception as exc:  # noqa: BLE001
                    logger.warning("ZAP active scan 失败: url=%s err=%s", url, exc)

            # 汇总 alerts
            alerts = self.zap.core.alerts()
            results: list[VulnItem] = []
            for alert in alerts:
                try:
                    name = alert.get("alert") or "Unknown"
                    risk = _risk_level_normalize(alert.get("risk"))
                    alert_url = alert.get("url") or ""
                    param = alert.get("param") or None
                    desc = alert.get("desc") or alert.get("description") or None
                    results.append(
                        VulnItem(
                            tool="ZAP",
                            vuln_name=name,
                            risk_level=risk,
                            url=alert_url,
                            param=param,
                            description=desc,
                        )
                    )
                except Exception:  # noqa: BLE001
                    continue

            logger.info("ZAP 扫描结束: vuln_count=%s elapsed_s=%.2f", len(results), time.time() - started)
            return True, results, "ZAP 扫描完成"
        except Exception as exc:  # noqa: BLE001
            logger.exception("ZAP 扫描异常: %s", exc)
            return False, [], "ZAP 扫描异常（可能未启动/被拦截/超时）"


class NucleiScanner:
    """
    nuclei 命令行扫描器封装。
    """

    def __init__(
        self,
        task_id: str,
        nuclei_path: str = NUCLEI_PATH,
        rate_limit: int = 5,
        retries: int = 2,
        timeout: int = 600,
        nuclei_vars: Optional[Dict[str, str]] = None,
        scan_profile: str = "auto",
    ) -> None:
        """
        :param nuclei_path: nuclei.exe 路径
        :param rate_limit: nuclei 的 --rate-limit
        :param retries: nuclei 的 --retries
        :param timeout: nuclei 执行超时时间（秒）
        """
        self.task_id = task_id
        self.nuclei_path = nuclei_path
        self.rate_limit = rate_limit
        self.retries = retries
        self.timeout = timeout
        # 对于“疑似卡死/极慢”的目标，给每个目标一个硬性上限，避免长时间占用进程
        self.max_urls = 50
        self.nuclei_vars = nuclei_vars or {}
        self.scan_profile = (scan_profile or "auto").strip().lower()

    def scan_urls(self, urls: List[str]) -> Tuple[bool, List[VulnItem], str]:
        """
        执行 nuclei 扫描并解析 JSONL 输出。

        通过 -jsonl 输出逐行 JSON，便于稳定解析。
        """
        if not urls:
            return True, [], "URL 列表为空"

        # 防止 URL 过多导致 nuclei 扫描时间不可控
        if len(urls) > self.max_urls:
            urls = urls[: self.max_urls]

        ua = _random_user_agent()
        proxy = _pick_proxy()

        cmd: list[str] = [
            self.nuclei_path,
            "-jsonl",
            "-rl",
            str(self.rate_limit),
            "-retries",
            str(self.retries),
            "-H",
            f"User-Agent: {ua}",
        ]

        # 若配置了定向模板，则优先使用（用于 CVE 定向快速验证，避免泛扫耗时）
        # 公网目标时剔除 DVWA 专用模板，避免无意义匹配拖慢流程。
        targeted = []
        for p in (NUCLEI_TARGETED_TEMPLATES or []):
            if not p or not isinstance(p, str):
                continue
            name = os.path.basename(p).lower()
            if name.startswith("dvwa-"):
                continue
            targeted.append(p)
        # 本地 DVWA：直接加载所有 dvwa-* 模板，避免 settings 顺序/缓存导致模块未执行
        first = urls[0] if urls else ""
        try:
            p0 = urlparse(first)
            host0 = (p0.hostname or "").lower()
            port0 = p0.port
        except Exception:  # noqa: BLE001
            host0, port0 = "", None

        auto_is_local_dvwa = host0 in ("127.0.0.1", "localhost") and (
            port0 == 7777 or (isinstance(first, str) and ":7777" in first)
        )
        profile_is_dvwa = self.scan_profile == "dvwa"
        profile_is_public = self.scan_profile == "public"
        is_local_dvwa = profile_is_dvwa or (not profile_is_public and auto_is_local_dvwa)
        if is_local_dvwa:
            dvwa_custom_dir = os.path.join(NUCLEI_TEMPLATES_DIR, "custom")
            dvwa_templates = sorted(
                [p for p in glob.glob(os.path.join(dvwa_custom_dir, "dvwa-*.yaml")) if p and os.path.isfile(p)]
            )
            if dvwa_templates:
                for p in dvwa_templates:
                    cmd.extend(["-t", p])
        elif targeted:
            for p in targeted:
                cmd.extend(["-t", p])

        # 传入模板变量（用于 dnslog_tag/dnslog_domain 等）
        for k, v in list(self.nuclei_vars.items())[:20]:
            if not k or v is None:
                continue
            cmd.extend(["-var", f"{k}={v}"])

        # 简单代理轮换：随机挑一个代理传给 nuclei
        if proxy:
            cmd.extend(["-proxy-url", proxy])

        logger.info("nuclei 扫描开始: urls=%s ua=%s proxy=%s", len(urls), ua, proxy or "none")
        started = time.time()

        # Windows 下 nuclei 的 `-l -`（stdin）不可用，这里落地到临时文件再传给 nuclei
        targets_path = ""
        try:
            with tempfile.NamedTemporaryFile(
                mode="w",
                encoding="utf-8",
                delete=False,
                prefix="nuclei_targets_",
                suffix=".txt",
            ) as fp:
                fp.write("\n".join(urls))
                fp.write("\n")
                targets_path = fp.name

            cmd_with_targets = [cmd[0], "-l", targets_path] + cmd[1:]

            process = subprocess.Popen(
                cmd_with_targets,
                stdin=subprocess.DEVNULL,
                stdout=subprocess.PIPE,
                stderr=subprocess.PIPE,
                text=True,
                encoding="utf-8",
            )

            stdout, stderr = process.communicate(timeout=self.timeout)
            if process.returncode != 0:
                err = (stderr or "").strip()
                logger.warning("nuclei 执行失败: code=%s stderr=%s", process.returncode, err)
                # 典型 WAF/网络/模板问题都会体现在 stderr
                return False, [], f"nuclei 执行失败：{err or process.returncode}"

            results: list[VulnItem] = []
            for line in stdout.splitlines():
                line = line.strip()
                if not line:
                    continue
                try:
                    item = json.loads(line)
                    name = item.get("info", {}).get("name") or item.get("template-id") or "Unknown"
                    severity = item.get("info", {}).get("severity") or "info"
                    matched = item.get("matched-at") or item.get("host") or ""
                    extracted = item.get("extracted-results")
                    desc = item.get("info", {}).get("description")
                    if extracted and not desc:
                        desc = "；".join([str(x) for x in extracted[:5]])

                    results.append(
                        VulnItem(
                            tool="nuclei",
                            vuln_name=str(name),
                            risk_level=_risk_level_normalize(severity),
                            url=str(matched),
                            param=None,
                            description=str(desc) if desc else None,
                        )
                    )
                except Exception:  # noqa: BLE001
                    continue

            logger.info("nuclei 扫描结束: vuln_count=%s elapsed_s=%.2f", len(results), time.time() - started)
            return True, results, "nuclei 扫描完成"
        except FileNotFoundError:
            # nuclei 未安装：记录警告并视为“扫描器功能降级”，避免任务整体失败
            logger.warning("未找到 nuclei 工具，将跳过 nuclei 扫描: %s", self.nuclei_path)
            return True, [], "未找到 nuclei 工具，请检查 NUCLEI_PATH 配置"
        except subprocess.TimeoutExpired:
            logger.warning("nuclei 扫描超时（>%s 秒）", self.timeout)
            try:
                process.kill()
            except Exception:  # noqa: BLE001
                pass
            try:
                # 尽量读完管道，避免僵尸进程
                process.communicate(timeout=5)
            except Exception:  # noqa: BLE001
                pass
            return False, [], "nuclei 扫描超时"
        except PermissionError as exc:
            logger.exception("nuclei 执行权限不足: %s", exc)
            return False, [], "nuclei 执行权限不足，请检查文件权限或杀软拦截"
        except Exception as exc:  # noqa: BLE001
            logger.exception("nuclei 扫描异常: %s", exc)
            return False, [], "nuclei 扫描异常（可能网络波动/WAF/模板问题）"
        finally:
            if targets_path:
                try:
                    os.remove(targets_path)
                except Exception:  # noqa: BLE001
                    pass


def scan_urls(
    task_id: str,
    urls: List[str],
    nuclei_vars: Optional[Dict[str, str]] = None,
    scan_profile: str = "auto",
) -> Tuple[bool, Dict[str, Any], str]:
    """
    统一的双引擎扫描入口。

    - 对 URL 列表执行合规校验（严格模式下拦截）；
    - 先运行 nuclei，再运行 ZAP（顺序可后续调整）；
    - 解析结果并写入数据库 Vulnerability 表。

    :param task_id: 扫描任务 ID（用于落库关联）
    :param urls: 待扫描 URL 列表
    :return: (是否成功, 汇总数据, 说明信息)
    """
    if not task_id or not isinstance(task_id, str):
        return False, {}, "task_id 不能为空"
    if not urls:
        return True, {"nuclei": 0, "zap": 0, "total": 0}, "URL 列表为空，跳过扫描"

    # 合规校验：任意 URL 的主机不在白名单时，严格模式下直接拒绝
    if STRICT_COMPLIANCE_MODE:
        for url in urls[:50]:
            domain = _extract_domain_from_url(url)
            ok, _ = check_authorized_domain(domain)
            if not ok:
                logger.warning("严格模式下目标未授权，拒绝扫描: task_id=%s domain=%s", task_id, domain)
                return False, {}, "严格模式下存在未授权目标，已拒绝扫描"

    started = time.time()
    logger.info("双引擎扫描开始: task_id=%s urls=%s", task_id, len(urls))

    mode = (scan_profile or "auto").strip().lower()
    nuclei_scanner = NucleiScanner(task_id=task_id, nuclei_vars=nuclei_vars or {}, scan_profile=mode)
    zap_scanner = ZapScanner(
        api_url=ZAP_API_URL,
        api_key=ZAP_API_KEY,
        timeout=int(ZAP_SCAN_TIMEOUT),
        delay_in_ms=int(max(0.0, float(REQUEST_DELAY)) * 1000),
    )

    summary: Dict[str, Any] = {"nuclei": 0, "zap": 0, "total": 0}
    errors: list[str] = []

    # nuclei 扫描
    ok_n, nuclei_vulns, msg_n = nuclei_scanner.scan_urls(urls)
    if ok_n:
        summary["nuclei"] = len(nuclei_vulns)
        for v in nuclei_vulns:
            add_vulnerability(
                task_id=task_id,
                tool=v.tool,
                vuln_name=v.vuln_name,
                risk_level=v.risk_level,
                url=v.url,
                param=v.param,
                description=v.description,
            )
    else:
        errors.append(msg_n)

    # Log4j（CVE-2021-44228）dnslog 二次确认：如果提供了 dnslog_tag/domain 则查询记录
    try:
        tag = (nuclei_vars or {}).get("dnslog_tag", "")
        dns_domain = (nuclei_vars or {}).get("dnslog_domain", "")
        if tag and dns_domain and DNSLOG_COOKIE:
            client = DNSLogClient(base_url=DNSLOG_BASE_URL, cookie=DNSLOG_COOKIE)
            ok_r, records = client.get_records()
            if ok_r and records:
                needle = f".{tag}.{dns_domain}".lower()
                hits = [r for r in records if needle in (r.name or "").lower()]
                if hits:
                    add_vulnerability(
                        task_id=task_id,
                        tool="dnslog",
                        vuln_name="Suspected Log4Shell (CVE-2021-44228)",
                        risk_level="high",
                        url=urls[0] if urls else "",
                        param=None,
                        description=f"DNSLog 回连命中 {len(hits)} 条：{', '.join([h.name for h in hits[:5]])}",
                    )
                    summary["nuclei"] = int(summary["nuclei"]) + 1
                    summary["total"] = int(summary["total"]) + 1
    except Exception as exc:  # noqa: BLE001
        logger.warning("DNSLog 二次确认异常（忽略不影响任务完成）: %s", exc)

    # ZAP 扫描：dvwa 模式直接跳过；public/auto 根据 URL 判断是否跳过本机回环目标
    skip_zap = False
    try:
        if mode == "dvwa":
            skip_zap = True
        else:
            sample = urls[:10] if urls else []
            if any(isinstance(u, str) and (":7777" in u or "127.0.0.1" in u or "localhost" in u) for u in sample):
                skip_zap = True
    except Exception:  # noqa: BLE001
        skip_zap = False

    if not skip_zap:
        ok_z, zap_vulns, msg_z = zap_scanner.scan_urls(urls)
        if ok_z:
            summary["zap"] = len(zap_vulns)
            for v in zap_vulns:
                add_vulnerability(
                    task_id=task_id,
                    tool=v.tool,
                    vuln_name=v.vuln_name,
                    risk_level=v.risk_level,
                    url=v.url,
                    param=v.param,
                    description=v.description,
                )
        else:
            errors.append(msg_z)
    else:
        logger.info("本地 DVWA 跳过 ZAP 扫描: task_id=%s", task_id)

    summary["total"] = int(summary["nuclei"]) + int(summary["zap"])

    elapsed = time.time() - started
    logger.info(
        "双引擎扫描结束: task_id=%s nuclei=%s zap=%s total=%s elapsed_s=%.2f",
        task_id,
        summary["nuclei"],
        summary["zap"],
        summary["total"],
        elapsed,
    )

    if errors:
        # 将 nuclei 超时 / ZAP 未启动等视为“功能降级”，不再让任务整体失败
        return True, summary, f"扫描完成（部分模块失败或未启用）：{'；'.join(errors)}"
    return True, summary, "扫描完成"


__all__ = ["ZapScanner", "NucleiScanner", "scan_urls"]

