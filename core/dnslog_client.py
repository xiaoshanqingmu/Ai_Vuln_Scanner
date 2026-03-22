import logging
from dataclasses import dataclass
from typing import Any, Dict, List, Optional, Tuple

import requests

logger = logging.getLogger("scan.dnslog")


@dataclass
class DNSLogRecord:
    raw: Dict[str, Any]

    @property
    def name(self) -> str:
        return str(self.raw.get("name") or self.raw.get("domain") or self.raw.get("subdomain") or "")

    @property
    def ip(self) -> str:
        return str(self.raw.get("ip") or self.raw.get("remote") or self.raw.get("addr") or "")

    @property
    def time(self) -> str:
        return str(self.raw.get("time") or self.raw.get("created_at") or self.raw.get("created") or "")


class DNSLogClient:
    """
    dnslog.cn 简易客户端（依赖浏览器 session cookie）。

    常见接口：
    - /getdomain.php  返回一个可用的子域名
    - /getrecords.php 返回查询记录（通常为 JSON）
    """

    def __init__(self, base_url: str, cookie: str, timeout: int = 10) -> None:
        self.base_url = base_url.rstrip("/")
        self.cookie = cookie.strip()
        self.timeout = timeout

    def _headers(self) -> Dict[str, str]:
        return {
            "User-Agent": "ai-vuln-scanner/1.0",
            "Cookie": self.cookie,
        }

    def get_domain(self) -> Tuple[bool, str]:
        if not self.cookie:
            return False, ""
        url = f"{self.base_url}/getdomain.php"
        try:
            r = requests.get(url, headers=self._headers(), timeout=self.timeout)
            if r.status_code != 200:
                logger.warning("dnslog getdomain 非 200: %s %s", r.status_code, r.text[:200])
                return False, ""
            domain = (r.text or "").strip()
            if not domain or "No Data" in domain:
                return False, ""
            return True, domain
        except Exception as exc:  # noqa: BLE001
            logger.warning("dnslog getdomain 异常: %s", exc)
            return False, ""

    def get_records(self) -> Tuple[bool, List[DNSLogRecord]]:
        if not self.cookie:
            return False, []
        url = f"{self.base_url}/getrecords.php"
        try:
            r = requests.get(url, headers=self._headers(), timeout=self.timeout)
            if r.status_code != 200:
                logger.warning("dnslog getrecords 非 200: %s %s", r.status_code, r.text[:200])
                return False, []
            text = (r.text or "").strip()
            if not text:
                return True, []
            # 尝试 JSON
            try:
                data = r.json()
            except Exception:  # noqa: BLE001
                # 有些实现返回纯文本；这里无法结构化，直接返回空并让上层降级
                logger.warning("dnslog getrecords 非 JSON 返回: %s", text[:200])
                return True, []

            items: List[Dict[str, Any]]
            if isinstance(data, list):
                items = data
            elif isinstance(data, dict):
                items = data.get("data") or data.get("records") or data.get("result") or []
                if isinstance(items, str):
                    items = []
            else:
                items = []

            return True, [DNSLogRecord(raw=i) for i in items if isinstance(i, dict)]
        except Exception as exc:  # noqa: BLE001
            logger.warning("dnslog getrecords 异常: %s", exc)
            return False, []


__all__ = ["DNSLogClient", "DNSLogRecord"]

