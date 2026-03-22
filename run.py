import logging

from app import create_app
from config.logging import setup_logging


def main() -> None:
    """
    项目启动入口。

    负责初始化日志配置与 Flask 应用，然后启动开发服务器。
    """
    setup_logging()
    logger = logging.getLogger("app")
    logger.info("启动 Flask 应用")

    app = create_app()
    app.run(host=app.config.get("HOST", "127.0.0.1"), port=app.config.get("PORT", 5000), debug=app.config.get("DEBUG", False))


if __name__ == "__main__":
    main()

