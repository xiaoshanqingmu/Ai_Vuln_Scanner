import os

from celery import Celery

from config.settings import BASE_DIR


def make_celery() -> Celery:
    """
    创建并配置 Celery 应用实例。

    使用 Redis 作为 broker 和结果后端，适配 Windows 环境：
    - 建议使用 `pool=solo` 启动 worker，以避免 Windows 下的多进程问题。
    """
    broker_url = os.getenv("CELERY_BROKER_URL", "redis://127.0.0.1:6379/0")
    backend_url = os.getenv("CELERY_RESULT_BACKEND", broker_url)

    celery_app = Celery(
        "ai_vuln_scanner",
        broker=broker_url,
        backend=backend_url,
        include=["core.async_task"],
    )

    celery_app.conf.update(
        timezone="UTC",
        task_serializer="json",
        accept_content=["json"],
        result_serializer="json",
        task_track_started=True,
        worker_max_tasks_per_child=100,
        task_time_limit=3700,  # 硬超时，略大于 3600 秒
        task_soft_time_limit=3600,  # 软超时，满足公网长任务要求
        broker_connection_retry_on_startup=True,
    )

    return celery_app


celery_app = make_celery()

