import logging
import re
import threading
import uuid
from typing import Any, Dict
from urllib.parse import urlsplit

from flask import Blueprint, jsonify, request

from core.async_task import run_full_scan, run_full_scan_sync
from core.compliance import check_authorized_domain
from database.db_operation import add_scan_task, get_scan_task_by_id

logger = logging.getLogger("app")

task_bp = Blueprint("task_api", __name__, url_prefix="/api/tasks")


# 允许任意公网靶场格式：
# - 域名/IP（可含字母数字点横线下划线）、可选端口
# - 或 http(s):// 开头的完整 URL（可携带路径）
_HOSTPORT_PATTERN = re.compile(r"^([a-zA-Z0-9._-]+|\d{1,3}(?:\.\d{1,3}){3})(?::\d{1,5})?$")
_URL_PATTERN = re.compile(r"^https?://", re.IGNORECASE)


def _normalize_target(raw: str) -> str:
    """
    将用户输入的“完整 URL”或“域名:端口”规范化为可用于扫描的 base URL。

    - 若输入为 http(s):// 开头，则保留 scheme://host:port + path（去掉 query/fragment，去掉末尾 /）
      例如：http://127.0.0.1/dvwa/ -> http://127.0.0.1/dvwa
    - 若输入为 host:port（或 host），则原样去掉末尾 /
    """
    s = (raw or "").strip()
    if not s:
        return s
    s = s.strip()
    if _URL_PATTERN.match(s):
        parts = urlsplit(s)
        if not parts.scheme or not parts.netloc:
            return ""

        # 保留 path 但尽量回退到“目录级 baseURL”
        # 例如：/dvwa/index.php -> /dvwa；/index.php -> /
        path = parts.path or ""

        # DVWA：漏洞模块通常以 /vulnerabilities/* 形式提供，
        # 对 nuclei/ZAP 我们更倾向于把 BaseURL 统一到“站点根”，
        # 让模板自行拼接具体漏洞路径，避免 path 被当成 host/base_path 导致拼接错误。
        if "/vulnerabilities/" in path.lower():
            path = ""

        if path and not path.endswith("/"):
            last = path.rsplit("/", 1)[-1]
            if "." in last:
                path = path.rsplit("/", 1)[0] if "/" in path else ""
        base = f"{parts.scheme}://{parts.netloc}{path}"
        return base.rstrip("/")
    return s.rstrip("/")


def _validate_target(normalized: str) -> bool:
    """
    校验 normalize 后的 target 是否符合允许格式。
    """
    if not normalized:
        return False
    if _URL_PATTERN.match(normalized):
        parts = urlsplit(normalized)
        hostport = parts.netloc
        return bool(hostport and _HOSTPORT_PATTERN.match(hostport))
    return bool(_HOSTPORT_PATTERN.match(normalized))


def _json_error(message: str, code: str, http_status: int = 400) -> Any:
    """
    统一 JSON 错误返回格式。
    """
    payload: Dict[str, Any] = {
        "success": False,
        "error": {
            "message": message,
            "code": code,
        },
    }
    return jsonify(payload), http_status


@task_bp.route("", methods=["POST"])
def create_task() -> Any:
    """
    创建扫描任务并提交异步执行。

    请求 JSON 参数：
    - domain: 目标地址（公网域名/IP+端口，例如 buuctf.cn:8080）
    - user_confirmed: 前端是否已完成授权确认（布尔值）
    """
    data = request.get_json(silent=True) or {}
    raw = (data.get("domain") or "").strip()
    user_confirmed = bool(data.get("user_confirmed"))

    if not raw:
        return _json_error("目标地址不能为空。", "INVALID_DOMAIN")

    domain = _normalize_target(raw)
    if not domain:
        return _json_error("目标地址格式不正确，请输入域名/IP或完整 URL（含端口）。", "INVALID_DOMAIN_FORMAT")

    if not _validate_target(domain):
        return _json_error("目标地址格式不正确，请输入域名/IP或完整 URL。", "INVALID_DOMAIN_FORMAT")

    # 合规审计（当前策略为允许所有公网靶场，此处仅记录）
    check_authorized_domain(domain)

    # 创建任务记录
    task_id = uuid.uuid4().hex
    ok, task, msg = add_scan_task(task_id=task_id, domain=domain, status="pending")
    if not ok or not task:
        return _json_error(f"任务创建失败：{msg}", "CREATE_TASK_FAILED", 500)

    try:
        # 提交 Celery 异步任务
        run_full_scan.apply_async(args=[task_id, domain])
        logger.info("异步任务已提交: task_id=%s domain=%s", task_id, domain)
        return jsonify({"success": True, "data": {"task_id": task_id, "queued": True}})
    except Exception as exc:  # noqa: BLE001
        logger.exception("异步任务提交失败（Redis/Celery 未启动?）: task_id=%s error=%s", task_id, exc)
        # 无 Redis 时改为本机后台线程同步执行，保证工具可完整使用
        thread = threading.Thread(target=run_full_scan_sync, args=(task_id, domain), daemon=True)
        thread.start()
        logger.info("已启动后台同步扫描: task_id=%s domain=%s", task_id, domain)
        return jsonify({
            "success": True,
            "data": {
                "task_id": task_id,
                "queued": True,
                "message": "Redis 不可用，已改为本机后台同步执行扫描，请到进度页查看。",
            },
        })


@task_bp.route("/<string:task_id>/status", methods=["GET"])
def get_task_status(task_id: str) -> Any:
    """
    查询任务状态，用于前端轮询展示进度。
    """
    task = None
    try:
        # 这里 get_scan_task_by_id 接受自增 ID，我们根据 task_id 搜索
        from database.models import ScanTask as TaskModel  # noqa: WPS433

        task = TaskModel.query.filter_by(task_id=task_id).first()
    except Exception as exc:  # noqa: BLE001
        logger.exception("任务状态查询异常: task_id=%s error=%s", task_id, exc)
        return _json_error("任务状态查询失败。", "STATUS_QUERY_FAILED", 500)

    if not task:
        return _json_error("任务不存在。", "TASK_NOT_FOUND", 404)

    status = task.status
    progress_text = {
        "pending": "等待开始（排队中）",
        "scanning": "URL 收集与漏洞扫描中",
        "analyzing": "AI 深度分析与报告生成中",
        "success": "任务已完成，可查看结果与下载报告",
        "failed": "任务执行失败，请查看日志或稍后重试",
    }.get(status, "未知状态")

    return jsonify(
        {
            "success": True,
            "data": {
                "task_id": task.task_id,
                "domain": task.domain,
                "status": status,
                "progress_text": progress_text,
            },
        }
    )


__all__ = ["task_bp"]

