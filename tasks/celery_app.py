"""
Celery 应用实例。

Windows 环境下启动 worker 示例（在虚拟环境中执行）：

    venv\Scripts\activate
    celery -A tasks.celery_app.celery_app worker -l info -P solo
"""

from celery_config import celery_app  # noqa: F401

