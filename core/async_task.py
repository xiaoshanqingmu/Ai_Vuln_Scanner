import logging
import os
import threading
import time
import re
from typing import Optional

from celery import shared_task
from celery.exceptions import SoftTimeLimitExceeded
import requests

from app import create_app
from config.settings import REQUEST_DELAY
from config.settings import DNSLOG_BASE_URL, DNSLOG_COOKIE
from config.settings import DVWA_COOKIE
from config.settings import SCAN_PROFILE
from core.dnslog_client import DNSLogClient
from core.report_generator import generate_excel_report, generate_pdf_report
from core.url_collector import GauCollector
from core.vuln_scanner import scan_urls as engine_scan_urls
from database.models import ScanTask

logger = logging.getLogger("scan")

# 为 Celery 任务创建独立的 Flask 应用上下文
flask_app = create_app()


def _resolve_scan_profile(domain: str) -> str:
    """
    解析当前任务应使用的扫描模式：public / dvwa。
    """
    mode = (SCAN_PROFILE or "auto").strip().lower()
    if mode in {"public", "dvwa"}:
        return mode
    s = (domain or "").lower()
    if "127.0.0.1" in s or "localhost" in s or ":7777" in s:
        return "dvwa"
    return "public"


def _extract_dvwa_token(html: str) -> str:
    """
    DVWA 在 login.php/security.php 页面中会渲染 user_token（单引号/双引号都兼容）。
    """
    if not html:
        return ""
    m = re.search(r"name=['\\\"]user_token['\\\"]\\s+value=['\\\"]([^'\\\"]+)['\\\"]", html)
    return m.group(1) if m else ""


def _dvwa_prepare_cookie(base_url: str, timeout_s: int = 10) -> Optional[str]:
    """
    自动登录 DVWA 并将 security 设为 low，返回可直接放入 nuclei Cookie header 的字符串。
    """
    base = (base_url or "").rstrip("/")
    if not base.startswith("http://") and not base.startswith("https://"):
        return None

    try:
        s = requests.Session()

        login = s.get(f"{base}/login.php", timeout=timeout_s)
        user_token = _extract_dvwa_token(login.text)
        if not user_token:
            return None

        s.post(
            f"{base}/login.php",
            data={"username": "admin", "password": "password", "Login": "Login", "user_token": user_token},
            allow_redirects=True,
            timeout=timeout_s,
        )

        sec = s.get(f"{base}/security.php", timeout=timeout_s)
        sec_token = _extract_dvwa_token(sec.text)
        if not sec_token:
            return None

        s.post(
            f"{base}/security.php",
            data={"security": "low", "seclev_submit": "Submit", "user_token": sec_token},
            allow_redirects=True,
            timeout=timeout_s,
        )

        cookies = s.cookies.get_dict()
        if not cookies:
            return None

        # nuclei 只需要 Cookie header 字符串
        return "; ".join([f"{k}={v}" for k, v in cookies.items()])
    except Exception as exc:  # noqa: BLE001
        logger.warning("DVWA 自动登录/准备 Cookie 失败: base=%s error=%s", base_url, exc)
        return None


def _update_task_status(task_id: str, status: str) -> None:
    """
    更新 ScanTask 的状态字段。

    :param task_id: ScanTask.task_id
    :param status: 新状态（pending / scanning / analyzing / success / failed）
    """
    from app import db  # noqa: WPS433

    with flask_app.app_context():
        task = ScanTask.query.filter_by(task_id=task_id).first()
        if not task:
            logger.warning("更新任务状态失败，任务不存在: task_id=%s", task_id)
            return
        task.status = status
        if status in ("success", "failed") and not task.finish_time:
            from datetime import datetime as _dt

            task.finish_time = _dt.utcnow()
        db.session.commit()
        logger.info("任务状态更新: task_id=%s status=%s", task_id, status)


def _start_stage_watchdog(task_id: str, max_scanning_seconds: int = 1200) -> threading.Event:
    """
    防卡死：若任务长时间停留在 scanning，则自动标记为 failed。

    :param task_id: ScanTask.task_id
    :param max_scanning_seconds: scanning 阶段最大允许时长（秒）
    :return: stop_event（任务结束时应 set 以终止 watchdog）
    """
    stop_event = threading.Event()

    def _worker() -> None:
        deadline = time.time() + float(max_scanning_seconds)
        while not stop_event.is_set():
            if time.time() > deadline:
                logger.error("scanning 阶段超时，自动失败: task_id=%s", task_id)
                _update_task_status(task_id, "failed")
                stop_event.set()
                return
            time.sleep(5)

    t = threading.Thread(target=_worker, daemon=True)
    t.start()
    return stop_event


@shared_task(
    bind=True,
    name="core.async_task.run_full_scan",
    max_retries=2,
    soft_time_limit=3600,
)
def run_full_scan(self, task_id: str, domain: str) -> str:
    """
    全流程异步扫描任务。

    包含：URL 收集 -> 漏洞扫描 -> AI 分析 -> 报告生成。
    """
    from app import db  # noqa: WPS433

    with flask_app.app_context():
        try:
            logger.info("异步任务开始: task_id=%s domain=%s", task_id, domain)
            _update_task_status(task_id, "scanning")
            watchdog = _start_stage_watchdog(task_id, max_scanning_seconds=1200)

            # 1. URL 收集
            scan_profile = _resolve_scan_profile(domain)
            local_dvwa = scan_profile == "dvwa"
            collector = GauCollector(
                domain,
                timeout=10 if local_dvwa else 45,
                max_retry=0 if local_dvwa else 1,
            )
            ok, urls, msg = collector.collect_and_filter(verify_http=False)
            if not ok or not urls:
                logger.error("URL 收集失败: task_id=%s msg=%s", task_id, msg)
                _update_task_status(task_id, "failed")
                watchdog.set()
                return f"URL 收集失败：{msg}"

            logger.info("URL 收集完成: task_id=%s url_count=%s", task_id, len(urls))
            # 防卡死：限制 URL 数量，避免对大型站点任务跑很久
            max_urls = 40 if local_dvwa else 60
            if len(urls) > max_urls:
                urls = urls[:max_urls]
                logger.info("URL 数量过多，已截断为 %s 条: task_id=%s", max_urls, task_id)

            # 2. 漏洞扫描
            nuclei_vars = {}
            # DVWA cookie-only：仅 dvwa 模式才注入 cookie 变量，避免污染公网模板变量
            if scan_profile == "dvwa":
                if DVWA_COOKIE:
                    nuclei_vars["dvwa_cookie"] = DVWA_COOKIE
                else:
                    auto_cookie = _dvwa_prepare_cookie(domain)
                    if auto_cookie:
                        nuclei_vars["dvwa_cookie"] = auto_cookie
            # 若配置了 dnslog.cn，则为 Log4j 模板注入 tag/domain 变量，便于回连确认
            if DNSLOG_COOKIE:
                try:
                    client = DNSLogClient(base_url=DNSLOG_BASE_URL, cookie=DNSLOG_COOKIE)
                    ok_d, dns_domain = client.get_domain()
                    if ok_d and dns_domain:
                        nuclei_vars["dnslog_domain"] = dns_domain
                        nuclei_vars["dnslog_tag"] = task_id[:8]
                except Exception as exc:  # noqa: BLE001
                    logger.warning("DNSLog 获取域名失败，将跳过 Log4j 回连确认: %s", exc)

            ok, summary, msg = engine_scan_urls(
                task_id=task_id,
                urls=urls,
                nuclei_vars=nuclei_vars,
                scan_profile=scan_profile,
            )
            logger.info(
                "漏洞扫描结果: task_id=%s ok=%s summary=%s msg=%s",
                task_id,
                ok,
                summary,
                msg,
            )
            if not ok:
                _update_task_status(task_id, "failed")
                watchdog.set()
                return f"漏洞扫描失败：{msg}"

            # 3. 报告生成（在报告模块内部触发 AI 分析以丰富内容）
            _update_task_status(task_id, "analyzing")
            watchdog.set()
            from config.settings import BASE_DIR  # noqa: WPS433

            reports_dir_excel = os.path.join(BASE_DIR, "reports", "excel")
            reports_dir_pdf = os.path.join(BASE_DIR, "reports", "pdf")
            os.makedirs(reports_dir_excel, exist_ok=True)
            os.makedirs(reports_dir_pdf, exist_ok=True)

            safe_domain = str(domain).replace(":", "_").replace("/", "_")
            filename_base = f"渗透测试报告_{task_id}_{safe_domain}"

            excel_path = os.path.join(reports_dir_excel, f"{filename_base}.xlsx")
            pdf_path = os.path.join(reports_dir_pdf, f"{filename_base}.pdf")

            generate_excel_report(task_id, excel_path)
            try:
                generate_pdf_report(task_id, pdf_path)
            except Exception as exc:  # noqa: BLE001
                logger.warning("PDF 报告生成失败，将不影响任务整体成功: %s", exc)

            _update_task_status(task_id, "success")
            logger.info("异步任务完成: task_id=%s", task_id)
            return "success"
        except SoftTimeLimitExceeded:
            logger.error("异步任务超时（超过 3600 秒）: task_id=%s", task_id)
            _update_task_status(task_id, "failed")
            raise
        except Exception as exc:  # noqa: BLE001
            logger.exception("异步任务执行异常: task_id=%s error=%s", task_id, exc)
            try:
                countdown = int(REQUEST_DELAY) or 10
            except Exception:
                countdown = 10
            if self.request.retries < self.max_retries:
                raise self.retry(exc=exc, countdown=countdown)
            _update_task_status(task_id, "failed")
            return f"异步任务执行异常：{exc}"


def run_full_scan_sync(task_id: str, domain: str) -> str:
    """
    全流程扫描（同步执行，不依赖 Celery/Redis）。

    与 run_full_scan 逻辑一致，用于无 Redis 时在后台线程中执行。
    """
    with flask_app.app_context():
        try:
            logger.info("同步扫描开始: task_id=%s domain=%s", task_id, domain)
            _update_task_status(task_id, "scanning")
            watchdog = _start_stage_watchdog(task_id, max_scanning_seconds=1200)

            scan_profile = _resolve_scan_profile(domain)
            local_dvwa = scan_profile == "dvwa"
            collector = GauCollector(
                domain,
                timeout=10 if local_dvwa else 45,
                max_retry=0 if local_dvwa else 1,
            )
            ok, urls, msg = collector.collect_and_filter(verify_http=False)
            if not ok or not urls:
                logger.error("URL 收集失败: task_id=%s msg=%s", task_id, msg)
                _update_task_status(task_id, "failed")
                watchdog.set()
                return f"URL 收集失败：{msg}"

            logger.info("URL 收集完成: task_id=%s url_count=%s", task_id, len(urls))
            max_urls = 40 if local_dvwa else 60
            if len(urls) > max_urls:
                urls = urls[:max_urls]
                logger.info("URL 数量过多，已截断为 %s 条: task_id=%s", max_urls, task_id)

            nuclei_vars = {}
            if scan_profile == "dvwa":
                if DVWA_COOKIE:
                    nuclei_vars["dvwa_cookie"] = DVWA_COOKIE
                else:
                    auto_cookie = _dvwa_prepare_cookie(domain)
                    if auto_cookie:
                        nuclei_vars["dvwa_cookie"] = auto_cookie
            if DNSLOG_COOKIE:
                try:
                    client = DNSLogClient(base_url=DNSLOG_BASE_URL, cookie=DNSLOG_COOKIE)
                    ok_d, dns_domain = client.get_domain()
                    if ok_d and dns_domain:
                        nuclei_vars["dnslog_domain"] = dns_domain
                        nuclei_vars["dnslog_tag"] = task_id[:8]
                except Exception as exc:  # noqa: BLE001
                    logger.warning("DNSLog 获取域名失败，将跳过 Log4j 回连确认: %s", exc)

            ok, summary, msg = engine_scan_urls(
                task_id=task_id,
                urls=urls,
                nuclei_vars=nuclei_vars,
                scan_profile=scan_profile,
            )
            logger.info("漏洞扫描结果: task_id=%s ok=%s summary=%s", task_id, ok, summary)
            if not ok:
                _update_task_status(task_id, "failed")
                watchdog.set()
                return f"漏洞扫描失败：{msg}"

            _update_task_status(task_id, "analyzing")
            watchdog.set()
            from config.settings import BASE_DIR
            reports_dir_excel = os.path.join(BASE_DIR, "reports", "excel")
            reports_dir_pdf = os.path.join(BASE_DIR, "reports", "pdf")
            os.makedirs(reports_dir_excel, exist_ok=True)
            os.makedirs(reports_dir_pdf, exist_ok=True)
            safe_domain = str(domain).replace(":", "_").replace("/", "_")
            filename_base = f"渗透测试报告_{task_id}_{safe_domain}"
            excel_path = os.path.join(reports_dir_excel, f"{filename_base}.xlsx")
            pdf_path = os.path.join(reports_dir_pdf, f"{filename_base}.pdf")
            generate_excel_report(task_id, excel_path)
            try:
                generate_pdf_report(task_id, pdf_path)
            except Exception as exc:
                logger.warning("PDF 报告生成失败: %s", exc)
            _update_task_status(task_id, "success")
            logger.info("同步扫描完成: task_id=%s", task_id)
            return "success"
        except Exception as exc:
            logger.exception("同步扫描异常: task_id=%s error=%s", task_id, exc)
            _update_task_status(task_id, "failed")
            return str(exc)


__all__ = ["run_full_scan", "run_full_scan_sync"]

