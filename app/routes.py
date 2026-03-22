import logging
import re
from typing import Any

from flask import Blueprint, Response, jsonify, redirect, render_template, request, url_for

from database.models import ScanTask, Vulnerability

logger = logging.getLogger("app")

main_bp = Blueprint("main", __name__)


_TARGET_PATTERN = re.compile(
    r"^(?P<host>[a-zA-Z0-9.-]+|\d{1,3}(?:\.\d{1,3}){3})(?::(?P<port>\d{1,5}))?$"
)


@main_bp.route("/", methods=["GET"])
def index() -> str:
    """
    任务提交首页。

    前端包含目标输入框与合规弹窗，提交后跳转到进度页面。
    """
    return render_template("index.html")


@main_bp.route("/tasks/<string:task_id>/progress", methods=["GET"])
def task_progress(task_id: str) -> str:
    """
    任务进度页面。

    页面通过前端轮询 /api/tasks/<task_id>/status 接口展示实时状态。
    """
    return render_template("task_progress.html", task_id=task_id)


@main_bp.route("/tasks/<string:task_id>/result", methods=["GET"])
def task_result(task_id: str) -> Any:
    """
    任务结果展示页面。

    展示漏洞列表、风险等级、AI 分析摘要，并提供报告下载按钮。
    """
    task = ScanTask.query.filter_by(task_id=task_id).first()
    if not task:
        return render_template("task_result.html", task=None, vulns=[])

    vulns = (
        Vulnerability.query.filter_by(task_id=task_id)
        .order_by(Vulnerability.risk_level.desc(), Vulnerability.create_time.asc())
        .all()
    )
    return render_template("task_result.html", task=task, vulns=vulns)


__all__ = ["main_bp"]

