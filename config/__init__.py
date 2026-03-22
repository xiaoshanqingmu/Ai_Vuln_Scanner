"""
配置模块初始化。

该包提供全局配置与日志配置，供应用其他模块统一引用。
"""

from .settings import (  # noqa: F401
    BASE_DIR,
    AUTHORIZED_DOMAINS,
    STRICT_COMPLIANCE_MODE,
    GAU_PATH,
    NUCLEI_PATH,
    ZAP_PATH,
    QWEN_API_KEY,
    QWEN_API_URL,
    REQUEST_DELAY,
    USER_AGENT_POOL,
    PROXY_LIST,
    DATABASE_URI,
    LOG_LEVEL,
    LOG_PATH,
    DEBUG,
    HOST,
    PORT,
)

