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

import requests

from config.settings import (
    NUCLEI_PATH,
    NUCLEI_TEMPLATES_DIR,
    NUCLEI_PUBLIC_TEMPLATE_DIRS,
    NUCLEI_CMS_TEMPLATE_DIRS,
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
    YXCMS_ADMIN_USER,
    YXCMS_ADMIN_PASS,
    YXCMS_LOGIN_PATH,
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


def _site_roots_from_urls(urls: List[str]) -> List[str]:
    """
    从 URL 列表推导站点根（兼容小皮子目录部署，如 /dvwa、/yxcms）。
    """
    roots: list[str] = []
    seen: set[str] = set()
    for u in urls or []:
        try:
            p = urlparse(u)
            if not p.scheme or not p.netloc:
                continue
            base = f"{p.scheme}://{p.netloc}"
            path = (p.path or "").strip("/")
            # 子目录部署：仅保留首段作为应用根
            if path:
                first = path.split("/", 1)[0]
                if first.lower() in {"dvwa", "yxcms"}:
                    base = f"{base}/{first}"
            if base not in seen:
                seen.add(base)
                roots.append(base.rstrip("/"))
        except Exception:  # noqa: BLE001
            continue
    return roots


def _filter_engine_vulns_for_lab(mode: str, items: List["VulnItem"]) -> List["VulnItem"]:
    """
    靶场模式下仅保留“更贴近源码漏洞点”的引擎结果，减少泛化告警噪音。
    """
    m = (mode or "").lower()
    if m not in {"dvwa", "cms"}:
        return items

    kept: list[VulnItem] = []
    for v in items or []:
        name = (v.vuln_name or "").lower()
        tool = (v.tool or "").lower()

        if m == "dvwa":
            # DVWA：优先保留自定义模板命中（与源码模块一一对应）
            if tool == "nuclei" and (v.vuln_name or "").startswith("DVWA -"):
                kept.append(v)
                continue
            # ZAP 仅保留 XSS/SQL 注入类
            if tool == "zap" and any(k in name for k in ("sql injection", "cross site scripting", "xss")):
                kept.append(v)
                continue
        elif m == "cms":
            # CMS：保留更直接的高风险漏洞类型，去掉安全头类泛告警
            if any(
                k in name
                for k in (
                    "sql injection",
                    "cross site scripting",
                    "xss",
                    "path traversal",
                    "file inclusion",
                    "command injection",
                    "remote code execution",
                    "ssrf",
                )
            ):
                kept.append(v)
                continue
            if "yxcms" in name:
                kept.append(v)
                continue
    return kept


def _probe_dvwa_source_vulns(urls: List[str]) -> List["VulnItem"]:
    """
    基于 DVWA 源码（sqli/xss_r/xss_d）的定向探测。
    """
    results: list[VulnItem] = []
    roots = _site_roots_from_urls(urls)
    if not roots:
        return results

    for root in roots[:2]:
        try:
            s = requests.Session()
            r = s.get(f"{root}/login.php", timeout=8)
            if r.status_code >= 400:
                continue
            import re

            m = re.search(r"name=['\"]user_token['\"]\s+value=['\"]([^'\"]+)['\"]", r.text or "")
            tok = m.group(1) if m else ""
            s.post(
                f"{root}/login.php",
                data={"username": "admin", "password": "password", "Login": "Login", "user_token": tok},
                allow_redirects=True,
                timeout=8,
            )
            sec = s.get(f"{root}/security.php", timeout=8)
            m2 = re.search(r"name=['\"]user_token['\"]\s+value=['\"]([^'\"]+)['\"]", sec.text or "")
            tok2 = m2.group(1) if m2 else ""
            s.post(
                f"{root}/security.php",
                data={"security": "low", "seclev_submit": "Submit", "user_token": tok2},
                allow_redirects=True,
                timeout=8,
            )

            # SQLi：源码 low.php 直接拼接 '$id' 进入 SQL
            sqli_url = f"{root}/vulnerabilities/sqli/?id=1%27&Submit=Submit"
            rs = s.get(sqli_url, timeout=8)
            body = rs.text or ""
            if any(k in body.lower() for k in ("sql syntax", "mysqli_", "warning", "mysql")):
                results.append(
                    VulnItem(
                        tool="source-probe",
                        vuln_name="DVWA Source-Proven SQL Injection (sqli low)",
                        risk_level="high",
                        url=sqli_url,
                        param="id",
                        description="命中 sqli/source/low.php 字符串拼接查询特征（源码级验证）。",
                    )
                )

            # Reflected XSS：low.php 直接回显 $_GET['name']
            payload = "<svg/onload=alert(1)>"
            xssr_url = f"{root}/vulnerabilities/xss_r/?name=%3Csvg%2Fonload%3Dalert(1)%3E"
            rr = s.get(xssr_url, timeout=8)
            if payload in (rr.text or ""):
                results.append(
                    VulnItem(
                        tool="source-probe",
                        vuln_name="DVWA Source-Proven Reflected XSS (xss_r low)",
                        risk_level="high",
                        url=xssr_url,
                        param="name",
                        description="命中 xss_r/source/low.php 未过滤回显（源码级验证）。",
                    )
                )

            # DOM XSS：源码 low.php 无防护，页面标识可确认模块暴露
            xssd_url = f"{root}/vulnerabilities/xss_d/?default=English"
            rd = s.get(xssd_url, timeout=8)
            if "DOM Based Cross Site Script" in (rd.text or ""):
                results.append(
                    VulnItem(
                        tool="source-probe",
                        vuln_name="DVWA Source-Proven DOM XSS Surface (xss_d low)",
                        risk_level="medium",
                        url=xssd_url,
                        param="default",
                        description="命中 xss_d/source/low.php（无防护）模块页面特征。",
                    )
                )
        except Exception:  # noqa: BLE001
            continue
    return results


def _probe_yxcms_source_vulns(urls: List[str]) -> List["VulnItem"]:
    """
    基于 YXCMS 源码的定向探测：
    - admin/commonController.php: GET phpsessid -> session_id()（会话固定风险）
    - default/extendController.php: inputName 直接拼接输出（反射型 XSS 面）
    """
    results: list[VulnItem] = []
    roots = _site_roots_from_urls(urls)
    if not roots:
        return results

    for root in roots[:2]:
        try:
            sid = "srcfix1234567890"
            u = f"{root}/index.php?r=admin/index/login&phpsessid={sid}"
            r = requests.get(u, timeout=8, allow_redirects=True)
            set_cookie = "; ".join(r.headers.get_all("Set-Cookie")) if hasattr(r.headers, "get_all") else (
                r.headers.get("Set-Cookie", "")
            )
            if sid in (set_cookie or ""):
                results.append(
                    VulnItem(
                        tool="source-probe",
                        vuln_name="YXCMS Source-Proven Session Fixation (phpsessid)",
                        risk_level="medium",
                        url=u,
                        param="phpsessid",
                        description="命中 admin/commonController.php 通过 GET 设置 session_id() 的源码行为。",
                    )
                )
        except Exception:  # noqa: BLE001
            pass

        try:
            payload = 'a"><svg/onload=alert(1)>'
            enc = "a%22%3E%3Csvg%2Fonload%3Dalert(1)%3E"
            u2 = f"{root}/index.php?r=default/extend/file&inputName={enc}"
            r2 = requests.get(u2, timeout=8, allow_redirects=True)
            body = r2.text or ""
            if payload in body or "inputName" in body and "form1" in body:
                results.append(
                    VulnItem(
                        tool="source-probe",
                        vuln_name="YXCMS Source-Proven Reflected XSS Surface (extend/file)",
                        risk_level="medium",
                        url=u2,
                        param="inputName",
                        description="命中 default/extendController.php 对 inputName 直接输出的源码路径。",
                    )
                )
        except Exception:  # noqa: BLE001
            pass

        # 会员退出 open redirect
        # 源码位置：protected/apps/member/controller/indexController.php::logout()
        try:
            evil = "http://example.com"
            u3 = f"{root}/index.php?r=member/index/logout&url={requests.utils.quote(evil, safe='')}"
            r3 = requests.get(u3, timeout=8, allow_redirects=False)
            loc = r3.headers.get("Location", "") or r3.headers.get("location", "") or ""
            body = (r3.text or "")[:2000]
            if evil in loc or evil in body:
                results.append(
                    VulnItem(
                        tool="source-probe",
                        vuln_name="YXCMS Source-Proven Open Redirect (member logout url)",
                        risk_level="medium",
                        url=u3,
                        param="url",
                        description="命中 member/indexController.php::logout() 直接使用 $_GET['url'] 跳转（源码级证据）。",
                    )
                )
        except Exception:  # noqa: BLE001
            pass

        # 会员中心 iframe src 可控（act 参数直传）
        # 源码位置：protected/apps/member/controller/indexController.php::index()
        # 视图位置：protected/apps/member/view/index_index.php (src="{$act}")
        try:
            payload = "javascript:alert(1)"
            u4 = f"{root}/index.php?r=member/index/index&act={requests.utils.quote(payload, safe='')}"
            r4 = requests.get(u4, timeout=8, allow_redirects=True)
            body4 = r4.text or ""
            if payload in body4 and "iframe" in body4.lower():
                results.append(
                    VulnItem(
                        tool="source-probe",
                        vuln_name="YXCMS Source-Proven Iframe Src Injection Surface (member index act)",
                        risk_level="medium",
                        url=u4,
                        param="act",
                        description="命中 member/indexController.php::index() 将 $_GET['act'] 直接传入 iframe src（源码级证据）。",
                    )
                )
        except Exception:  # noqa: BLE001
            pass

        # 后台模板管理：tpgetcode 任意文件读取（路径拼接无穿越拦截）
        # 源码位置：protected/apps/admin/controller/setController.php::tpgetcode()
        try:
            s = requests.Session()
            login_url = f"{root}{YXCMS_LOGIN_PATH}"
            _ = s.post(
                login_url,
                data={"username": YXCMS_ADMIN_USER, "password": YXCMS_ADMIN_PASS},
                allow_redirects=True,
                timeout=10,
            )

            tpget_url = f"{root}/index.php?r=admin/set/tpgetcode"
            # 目标文件：protected/config.php（含 allowType/DB 配置等关键字）
            candidates = [
                ("default", "../../../../protected/config.php"),
                ("default", "../../../protected/config.php"),
                ("default", "../../protected/config.php"),
            ]
            for mname, fname in candidates:
                rr = s.post(tpget_url, data={"Mname": mname, "fname": fname}, timeout=10)
                body = rr.text or ""
                if any(k in body for k in ("allowType", "DB_HOST", "DB_NAME", "DB_USER", "DB_PREFIX")):
                    results.append(
                        VulnItem(
                            tool="source-probe",
                            vuln_name="YXCMS Source-Proven Arbitrary File Read (admin set/tpgetcode path traversal)",
                            risk_level="high",
                            url=tpget_url,
                            param="Mname,fname",
                            description=(
                                "命中 setController::tpgetcode() 文件路径拼接读取："
                                f"Mname={mname} fname={fname}，响应包含配置关键字（源码级证据）。"
                            ),
                        )
                    )
                    break
        except Exception:  # noqa: BLE001
            pass
    return results


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
        scan_profile: str = "auto",
    ) -> None:
        """
        :param api_url: ZAP API 地址（默认本机 8080）
        :param api_key: ZAP API Key（若开启）
        :param timeout: ZAP API 操作超时时间（秒）
        :param delay_in_ms: ZAP 请求延迟（毫秒），用于公网适配
        """
        self.timeout = timeout
        self.api_url = api_url
        self.delay_in_ms = delay_in_ms
        self.api_key = api_key
        self.scan_profile = (scan_profile or "auto").strip().lower()
        self.session = requests.Session()

    def _zap_call(self, path: str, params: Optional[Dict[str, Any]] = None, timeout: int = 10) -> Dict[str, Any]:
        """
        直接调用 ZAP JSON API，规避 zapv2 代理模式在本机环境下不稳定的问题。
        """
        url = f"{self.api_url.rstrip('/')}/JSON/{path.lstrip('/')}"
        q: Dict[str, Any] = {"apikey": self.api_key}
        if params:
            q.update(params)
        resp = self.session.get(url, params=q, timeout=timeout)
        resp.raise_for_status()
        return resp.json()

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
            m = re.search(r"name=['\"]user_token['\"]\s+value=['\"]([^'\"]+)['\"]", r.text or "")
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
            m2 = re.search(r"name=['\"]user_token['\"]\s+value=['\"]([^'\"]+)['\"]", sec.text or "")
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

    def _yxcms_prepare_session_cookie(self, base_url: str) -> Tuple[bool, str]:
        """
        YXCMS 专用：登录后台获取会话 Cookie，用于 ZAP 携带认证态扫描。
        """
        try:
            import requests

            s = requests.Session()
            login_url = f"{base_url}{YXCMS_LOGIN_PATH}"
            # 常见后台登录表单字段：username/password
            resp = s.post(
                login_url,
                data={"username": YXCMS_ADMIN_USER, "password": YXCMS_ADMIN_PASS},
                allow_redirects=True,
                timeout=10,
            )
            if resp.status_code >= 400:
                return False, "YXCMS 登录失败（HTTP 错误）"

            ck = s.cookies.get_dict()
            if not ck:
                return False, "YXCMS 未获得会话 Cookie"
            cookie_header = "; ".join([f"{k}={v}" for k, v in ck.items()])
            return True, cookie_header
        except Exception as exc:  # noqa: BLE001
            logger.warning("YXCMS 会话准备失败: %s", exc)
            return False, "YXCMS 会话准备失败"

    def _zap_set_cookie_replacer(self, cookie_header: str) -> None:
        """
        给 ZAP 加一条 replacer 规则，强制携带 Cookie（用于已登录会话）。
        """
        try:
            _ = self._zap_call(
                "replacer/action/addRule/",
                {
                    "description": "dvwa-cookie",
                    "enabled": "true",
                    "matchType": "REQ_HEADER",
                    "matchRegex": "false",
                    "matchString": "Cookie",
                    "replacement": cookie_header,
                },
                timeout=10,
            )
        except Exception as exc:  # noqa: BLE001
            logger.warning("ZAP 设置 Cookie replacer 失败（将继续执行）: %s", exc)

    def _ensure_zap_ready(self) -> Tuple[bool, str]:
        """
        检查 ZAP 服务是否可用。
        """
        try:
            data = self._zap_call("core/view/version/", timeout=8)
            _ = data.get("version", "")
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
            # 设置延迟（部分 API 版本可能不支持，因此容错）
            try:
                _ = self._zap_call("ascan/action/setOptionDelayInMs/", {"Integer": str(self.delay_in_ms)}, timeout=8)
            except Exception:  # noqa: BLE001
                logger.warning("ZAP 设置 delayInMs 失败，继续执行")

            # DVWA/YXCMS：尽量准备认证 Cookie，提高后台覆盖率
            try:
                base = urls[0].split("?", 1)[0].rstrip("/")
                parsed = urlparse(base)
                base_url = f"{parsed.scheme}://{parsed.netloc}"
                if self.scan_profile == "dvwa":
                    ok_ck, cookie_header = self._dvwa_prepare_session_cookie(base_url)
                    if ok_ck and cookie_header:
                        self._zap_set_cookie_replacer(cookie_header)
                        logger.info("DVWA 会话已准备，ZAP 将携带 Cookie 扫描: %s", base_url)
                elif self.scan_profile == "cms":
                    ok_ck, cookie_header = self._yxcms_prepare_session_cookie(base_url)
                    if ok_ck and cookie_header:
                        self._zap_set_cookie_replacer(cookie_header)
                        logger.info("YXCMS 会话已准备，ZAP 将携带 Cookie 扫描: %s", base_url)
            except Exception:  # noqa: BLE001
                pass

            # 先 spider / ajax spider（覆盖更多路径），再 active scan
            seed = urls[0]
            # CMS 场景下 spider 的种子如果选到深层路径，会导致爬取范围失控；
            # 这里强制改为站点根地址，显著缩短 scanning 阶段。
            if self.scan_profile == "cms":
                p_seed = urlparse(seed)
                if p_seed.scheme and p_seed.netloc:
                    seed = f"{p_seed.scheme}://{p_seed.netloc}"

            # DVWA 场景：为保证“尽快出报告”，跳过 spider/ajaxSpider，
            # 只对少量关键入口做主动扫描（xss_d/sqli 等）。
            skip_spider = self.scan_profile == "dvwa"
            if not skip_spider:
                try:
                    spider_id = str(self._zap_call("spider/action/scan/", {"url": seed}, timeout=15).get("scan", "0"))
                    while True:
                        status = int(
                            self._zap_call("spider/view/status/", {"scanId": spider_id}, timeout=8).get("status", "0")
                        )
                        if status >= 100:
                            break
                        time.sleep(2)
                        if time.time() - started > self.timeout:
                            raise TimeoutError("ZAP spider 超时")
                except Exception as exc:  # noqa: BLE001
                    logger.warning("ZAP spider 失败（将继续）：%s", exc)

                try:
                    _ = self._zap_call("ajaxSpider/action/scan/", {"url": seed}, timeout=12)
                    # ajaxSpider 没有百分比状态，用固定时间窗口轮询
                    deadline = time.time() + min(120, max(20, int(self.timeout / 5)))
                    while time.time() < deadline:
                        status = str(self._zap_call("ajaxSpider/view/status/", timeout=8).get("status", "")).lower()
                        if status in {"stopped", "complete"}:
                            break
                        time.sleep(2)
                except Exception as exc:  # noqa: BLE001
                    logger.warning("ZAP ajax spider 失败（将继续）：%s", exc)

            # 逐个 URL 做访问与主动扫描（避免一次性对公网压力过大）
            # CMS 场景下 urls 可能很多；但 spider/ajaxSpider 已能覆盖大量路径，
            # 因此主动扫描只挑“后台入口/登录相关”的少量目标，显著缩短扫描时长。
            scan_targets = urls
            if self.scan_profile == "cms":
                scan_targets = []
                seen: set[str] = set()
                for u in urls:
                    lu = (u or "").lower()
                    if not lu or lu in seen:
                        continue
                    if any(
                        k in lu
                        for k in (
                            "/index.php?r=admin",
                            "/index.php?r=admin/login",
                            "/admin",
                            "/login.php",
                            "/index.php",
                        )
                    ):
                        scan_targets.append(u)
                        seen.add(lu)
                    if len(scan_targets) >= 6:
                        break
                if not scan_targets:
                    scan_targets = urls[:2]
            elif self.scan_profile == "dvwa":
                scan_targets = []
                seen = set()
                dvwa_keys = (
                    "/vulnerabilities/xss_d",
                    "/vulnerabilities/sqli",
                    "/vulnerabilities/sql",
                    "/vulnerabilities/xss",
                    "/vulnerabilities/",
                    "/index.php",
                )
                for u in urls:
                    lu = (u or "").lower()
                    if not lu or lu in seen:
                        continue
                    if any(k in lu for k in dvwa_keys):
                        scan_targets.append(u)
                        seen.add(lu)
                    if len(scan_targets) >= 6:
                        break
                if not scan_targets:
                    scan_targets = urls[:2]

            for url in scan_targets:
                time.sleep(max(0.0, float(REQUEST_DELAY)))
                try:
                    _ = self._zap_call("core/action/accessUrl/", {"url": url, "followRedirects": "true"}, timeout=12)
                except Exception:  # noqa: BLE001
                    logger.debug("ZAP access_url 失败（可能被 WAF/网络波动）：%s", url)

                try:
                    scan_id = str(self._zap_call("ascan/action/scan/", {"url": url}, timeout=15).get("scan", "0"))
                    # 等待扫描完成
                    while True:
                        status = int(self._zap_call("ascan/view/status/", {"scanId": scan_id}, timeout=8).get("status", "0"))
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

            # 汇总 alerts：不依赖 scheme/baseurl 精确匹配，按 host 过滤，避免 http/https 混用导致 0 结果。
            seed_host = (urlparse(seed).netloc or "").lower()
            all_alerts = self._zap_call("core/view/alerts/", {"start": "0", "count": "9999"}, timeout=20).get("alerts", [])
            alerts = []
            for a in all_alerts:
                u = str(a.get("url") or "")
                h = (urlparse(u).netloc or "").lower()
                if not seed_host or h == seed_host:
                    alerts.append(a)
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

        # 公网目标允许更长时间；cms 本地靶场优先快速出结果，避免长时间卡在 nuclei。
        if self.scan_profile == "public":
            self.timeout = max(self.timeout, 1200)
        elif self.scan_profile == "cms":
            self.timeout = min(max(self.timeout, 120), 300)

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
        targeted: List[str] = []
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
        else:
            # public/cms：加载通用模板目录（否则只靠少数定向 CVE 命中率极低）
            if self.scan_profile == "public":
                dirs = NUCLEI_PUBLIC_TEMPLATE_DIRS
            elif self.scan_profile == "cms":
                dirs = NUCLEI_CMS_TEMPLATE_DIRS
            else:
                dirs = []
            for d in dirs:
                if d and os.path.isdir(d):
                    cmd.extend(["-t", d])
            # 同时加载定向模板（补充 CVE 快速验证）
            if targeted:
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
        scan_profile=mode,
    )

    summary: Dict[str, Any] = {"nuclei": 0, "zap": 0, "source": 0, "total": 0}
    errors: list[str] = []

    # cms 模式：先跑 ZAP，快速拿到可验证结果，避免 nuclei 阻塞整条链路。
    zap_ran = False
    if mode == "cms":
        ok_z, zap_vulns, msg_z = zap_scanner.scan_urls(urls)
        if ok_z:
            zap_ran = True
            zap_vulns = _filter_engine_vulns_for_lab(mode, zap_vulns)
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

        # 只有在 ZAP 没出结果时才补跑 nuclei，控制总时长。
        if summary["zap"] == 0:
            ok_n, nuclei_vulns, msg_n = nuclei_scanner.scan_urls(urls)
            if ok_n:
                nuclei_vulns = _filter_engine_vulns_for_lab(mode, nuclei_vulns)
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
    else:
        # nuclei 扫描
        ok_n, nuclei_vulns, msg_n = nuclei_scanner.scan_urls(urls)
        if ok_n:
            nuclei_vulns = _filter_engine_vulns_for_lab(mode, nuclei_vulns)
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

    # ZAP 扫描：
    # - dvwa 模式不再跳过：部分交互/DOM 型漏洞仅 ZAP 能产出证据
    # - cms 模式已在上面执行（避免重复跑）
    skip_zap = False
    try:
        if mode == "cms" and zap_ran:
            skip_zap = True
    except Exception:  # noqa: BLE001
        skip_zap = False

    if not skip_zap:
        ok_z, zap_vulns, msg_z = zap_scanner.scan_urls(urls)
        if ok_z:
            zap_vulns = _filter_engine_vulns_for_lab(mode, zap_vulns)
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
        logger.info("跳过 ZAP 扫描: task_id=%s", task_id)

    # 源码对齐探测：补充真实漏洞证据（避免仅依赖模板/规则）
    source_vulns: list[VulnItem] = []
    try:
        if mode == "dvwa":
            source_vulns = _probe_dvwa_source_vulns(urls)
        elif mode == "cms":
            source_vulns = _probe_yxcms_source_vulns(urls)
    except Exception as exc:  # noqa: BLE001
        logger.warning("源码对齐探测异常（已忽略）: %s", exc)

    if source_vulns:
        summary["source"] = len(source_vulns)
        for v in source_vulns:
            add_vulnerability(
                task_id=task_id,
                tool=v.tool,
                vuln_name=v.vuln_name,
                risk_level=v.risk_level,
                url=v.url,
                param=v.param,
                description=v.description,
            )

    summary["total"] = int(summary["nuclei"]) + int(summary["zap"]) + int(summary.get("source", 0))

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

