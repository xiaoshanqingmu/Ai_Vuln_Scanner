import logging
from flask import Flask
from flask_sqlalchemy import SQLAlchemy
from flask_migrate import Migrate
from celery import Celery  # 新增：导入 Celery

from config.settings import (
    DATABASE_URI,
    DEBUG,
    HOST,
    PORT,
)

# 原有代码：初始化数据库相关扩展
db = SQLAlchemy()
migrate = Migrate()

# ===================== 新增：Celery 核心配置 =====================
# 配置 Redis 连接（确保和你的 Redis 端口一致，默认 6379）
REDIS_URL = "redis://localhost:6379/0"

# 定义全局的 celery_app 实例（关键！必须叫这个名字）
celery_app = Celery(
    "vuln_scanner",  # 任务名称（自定义）
    broker=REDIS_URL,  # 消息队列地址（Redis）
    backend=REDIS_URL,  # 任务结果存储地址（Redis）
    include=[]  # 后续添加任务文件时，填路径如 ["app.tasks"]
)

# Celery 适配 Windows + Flask 工厂模式的配置
celery_app.conf.update(
    task_serializer="json",
    accept_content=["json"],
    result_serializer="json",
    timezone="Asia/Shanghai",
    enable_utc=True,
    worker_pool="solo"  # Windows 必须用 solo 池
)
# ===================== Celery 配置结束 =====================


def create_app() -> Flask:
    """
    Flask 应用工厂函数。

    负责创建并配置 Flask 实例，初始化数据库等扩展。
    """
    app = Flask(__name__)

    # 基础配置
    app.config["SQLALCHEMY_DATABASE_URI"] = DATABASE_URI
    app.config["SQLALCHEMY_TRACK_MODIFICATIONS"] = False
    app.config["DEBUG"] = DEBUG
    app.config["HOST"] = HOST
    app.config["PORT"] = PORT

    # 初始化扩展
    db.init_app(app)
    migrate.init_app(app, db)

    # 确保数据库表存在（首次启动时创建 scan_tasks、vulnerabilities 等）
    with app.app_context():
        from database.models import ScanTask, Vulnerability  # noqa: F401
        db.create_all()

    # 注册蓝图或路由（后续可继续补充）
    try:
        from .routes import main_bp  # noqa: WPS433

        app.register_blueprint(main_bp)
    except ImportError:
        # 初期尚未实现 routes 时，不影响应用启动
        pass

    # 注册合规相关接口蓝图
    try:
        from .api.compliance_api import compliance_bp  # noqa: WPS433

        app.register_blueprint(compliance_bp)
    except ImportError:
        # 若接口模块尚未准备好，同样不阻塞应用启动
        pass

    # 注册报告下载相关接口蓝图
    try:
        from .api.report_api import report_bp  # noqa: WPS433

        app.register_blueprint(report_bp)
    except ImportError:
        pass

    # 注册任务相关接口蓝图
    try:
        from .api.task_api import task_bp  # noqa: WPS433

        app.register_blueprint(task_bp)
    except ImportError:
        pass

    # 日志
    logger = logging.getLogger("app")
    logger.info("Flask 应用已创建")

    return app


__all__ = ["create_app", "db", "celery_app"]  # 新增：导出 celery_app