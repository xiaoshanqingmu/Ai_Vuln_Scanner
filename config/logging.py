import os
import logging
from logging.config import dictConfig

from .settings import LOG_PATH, LOG_LEVEL


def _ensure_log_dir_exists(log_dir: str) -> None:
    """
    确保日志目录存在，若不存在则自动创建。

    :param log_dir: 日志目录路径
    """
    if not os.path.exists(log_dir):
        os.makedirs(log_dir, exist_ok=True)


# 确保日志目录已创建
_ensure_log_dir_exists(LOG_PATH)

# 各模块日志文件路径
APP_LOG_FILE = os.path.join(LOG_PATH, "app.log")
COMPLIANCE_LOG_FILE = os.path.join(LOG_PATH, "compliance.log")
AI_LOG_FILE = os.path.join(LOG_PATH, "ai.log")
SCAN_LOG_FILE = os.path.join(LOG_PATH, "scan.log")
REPORT_LOG_FILE = os.path.join(LOG_PATH, "report.log")

# 日志格式中包含完整消息内容，业务代码在记录日志时可以写入完整公网地址（含端口）
STANDARD_FORMAT = (
    "%(asctime)s [%(levelname)s] [%(name)s] "
    "%(filename)s:%(lineno)d - %(message)s"
)

SIMPLE_FORMAT = "%(levelname)s [%(name)s] - %(message)s"


LOGGING_CONFIG = {
    "version": 1,
    "disable_existing_loggers": False,
    "formatters": {
        "standard": {
            "format": STANDARD_FORMAT,
        },
        "simple": {
            "format": SIMPLE_FORMAT,
        },
    },
    "handlers": {
        # 控制台输出，用于开发调试
        "console": {
            "class": "logging.StreamHandler",
            "level": LOG_LEVEL,
            "formatter": "simple",
        },
        # 通用应用日志（按天滚动）
        "app_file": {
            "class": "logging.handlers.TimedRotatingFileHandler",
            "level": LOG_LEVEL,
            "formatter": "standard",
            "filename": APP_LOG_FILE,
            "when": "midnight",
            "backupCount": 30,
            "encoding": "utf-8",
        },
        # 合规模块日志
        "compliance_file": {
            "class": "logging.handlers.TimedRotatingFileHandler",
            "level": LOG_LEVEL,
            "formatter": "standard",
            "filename": COMPLIANCE_LOG_FILE,
            "when": "midnight",
            "backupCount": 30,
            "encoding": "utf-8",
        },
        # AI 分析模块日志
        "ai_file": {
            "class": "logging.handlers.TimedRotatingFileHandler",
            "level": LOG_LEVEL,
            "formatter": "standard",
            "filename": AI_LOG_FILE,
            "when": "midnight",
            "backupCount": 30,
            "encoding": "utf-8",
        },
        # 扫描模块日志
        "scan_file": {
            "class": "logging.handlers.TimedRotatingFileHandler",
            "level": LOG_LEVEL,
            "formatter": "standard",
            "filename": SCAN_LOG_FILE,
            "when": "midnight",
            "backupCount": 30,
            "encoding": "utf-8",
        },
        # 报告模块日志
        "report_file": {
            "class": "logging.handlers.TimedRotatingFileHandler",
            "level": LOG_LEVEL,
            "formatter": "standard",
            "filename": REPORT_LOG_FILE,
            "when": "midnight",
            "backupCount": 30,
            "encoding": "utf-8",
        },
    },
    "loggers": {
        # Flask 应用与通用日志
        "app": {
            "level": LOG_LEVEL,
            "handlers": ["console", "app_file"],
            "propagate": False,
        },
        # 合规模块日志记录器
        "compliance": {
            "level": LOG_LEVEL,
            "handlers": ["console", "compliance_file"],
            "propagate": False,
        },
        # AI 分析模块日志记录器
        "ai": {
            "level": LOG_LEVEL,
            "handlers": ["console", "ai_file"],
            "propagate": False,
        },
        # 漏洞扫描模块日志记录器
        "scan": {
            "level": LOG_LEVEL,
            "handlers": ["console", "scan_file"],
            "propagate": False,
        },
        # 报告生成模块日志记录器
        "report": {
            "level": LOG_LEVEL,
            "handlers": ["console", "report_file"],
            "propagate": False,
        },
    },
    # 根日志记录器（如未指定 logger 名称时使用）
    "root": {
        "level": LOG_LEVEL,
        "handlers": ["console", "app_file"],
    },
}


def setup_logging() -> None:
    """
    初始化全局日志配置。

    在应用启动时（如 run.py 或 app/__init__.py 中）调用此函数，
    即可完成分模块日志配置与按天滚动的日志文件创建。
    """
    # 再次确保日志目录存在，防止外部调用时目录被删除
    _ensure_log_dir_exists(LOG_PATH)
    dictConfig(LOGGING_CONFIG)
    logging.getLogger("app").info("日志系统已初始化")

