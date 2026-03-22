"""
数据库模块。

该包封装了所有与数据库相关的模型定义与基础操作。
"""

from .models import ScanTask, Vulnerability  # noqa: F401
from .db_operation import (  # noqa: F401
    init_db,
    add_scan_task,
    get_scan_task_by_id,
    update_scan_task_status,
    get_all_scan_tasks,
    add_vulnerability,
    get_vulnerabilities_by_task_id,
    update_vulnerability_ai_info,
)

__all__ = [
    "ScanTask",
    "Vulnerability",
    "init_db",
    "add_scan_task",
    "get_scan_task_by_id",
    "update_scan_task_status",
    "get_all_scan_tasks",
    "add_vulnerability",
    "get_vulnerabilities_by_task_id",
    "update_vulnerability_ai_info",
]

