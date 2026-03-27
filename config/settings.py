import os
from dotenv import load_dotenv

# 获取项目根目录，确保在 Windows 下路径兼容
BASE_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))

# 加载 .env 文件中的环境变量
ENV_PATH = os.path.join(BASE_DIR, ".env")
if os.path.exists(ENV_PATH):
    load_dotenv(ENV_PATH)


# =========================
# 合规配置（当前策略：不启用白名单，允许任意公网靶场 URL）
# =========================

# 授权域名白名单。留空 [] 表示不限制，任意公网靶场均可扫描
AUTHORIZED_DOMAINS = []

# 严格合规模式：当前未使用白名单，此选项仅影响“用户授权确认”是否必填
STRICT_COMPLIANCE_MODE = False


# =========================
# 外部工具路径配置（Windows 可执行文件）
# =========================

# gau 工具的可执行文件路径（Windows 下通常为 gau.exe）
# 默认指向项目内 tools 目录，实际使用前请确认并替换
# TODO: 替换为实际 gau.exe 所在路径
GAU_PATH = os.path.join(BASE_DIR, "tools", "gau.exe")

# nuclei 工具的可执行文件路径（Windows 下通常为 nuclei.exe）
# TODO: 替换为实际 nuclei.exe 所在路径
NUCLEI_PATH = os.path.join(BASE_DIR, "tools", "nuclei.exe")

# nuclei 模板目录（默认指向项目内 tools/nuclei-templates）
NUCLEI_TEMPLATES_DIR = os.path.join(BASE_DIR, "tools", "nuclei-templates")

# 可选：定向模板（用于快速验证特定 CVE，避免泛扫卡死）
NUCLEI_TARGETED_TEMPLATES = [
    os.path.join(NUCLEI_TEMPLATES_DIR, "cves", "2017", "CVE-2017-7525.yaml"),
    os.path.join(NUCLEI_TEMPLATES_DIR, "cves", "2021", "CVE-2021-44228.yaml"),
    os.path.join(NUCLEI_TEMPLATES_DIR, "custom", "dvwa-sqli-login.yaml"),
    os.path.join(NUCLEI_TEMPLATES_DIR, "custom", "dvwa-login-page.yaml"),
    os.path.join(NUCLEI_TEMPLATES_DIR, "custom", "dvwa-brute.yaml"),
    os.path.join(NUCLEI_TEMPLATES_DIR, "custom", "dvwa-exec.yaml"),
    os.path.join(NUCLEI_TEMPLATES_DIR, "custom", "dvwa-csrf.yaml"),
    os.path.join(NUCLEI_TEMPLATES_DIR, "custom", "dvwa-fi.yaml"),
    os.path.join(NUCLEI_TEMPLATES_DIR, "custom", "dvwa-upload.yaml"),
    os.path.join(NUCLEI_TEMPLATES_DIR, "custom", "dvwa-captcha.yaml"),
    os.path.join(NUCLEI_TEMPLATES_DIR, "custom", "dvwa-sqli-blind.yaml"),
    os.path.join(NUCLEI_TEMPLATES_DIR, "custom", "dvwa-weak-id.yaml"),
    os.path.join(NUCLEI_TEMPLATES_DIR, "custom", "dvwa-xss-dom.yaml"),
    os.path.join(NUCLEI_TEMPLATES_DIR, "custom", "dvwa-xss-reflected.yaml"),
    os.path.join(NUCLEI_TEMPLATES_DIR, "custom", "dvwa-xss-s.yaml"),
    os.path.join(NUCLEI_TEMPLATES_DIR, "custom", "dvwa-csp.yaml"),
    os.path.join(NUCLEI_TEMPLATES_DIR, "custom", "dvwa-javascript.yaml"),
]

# 公网模板目录（用于 public/cms 等场景，避免只靠少量定向模板导致命中率过低）
NUCLEI_PUBLIC_TEMPLATE_DIRS = [
    os.path.join(NUCLEI_TEMPLATES_DIR, "vulnerabilities"),
    os.path.join(NUCLEI_TEMPLATES_DIR, "misconfiguration"),
    os.path.join(NUCLEI_TEMPLATES_DIR, "exposed-panels"),
    os.path.join(NUCLEI_TEMPLATES_DIR, "exposures"),
]

# CMS/靶场快速模式：先跑“暴露面+配置类”模板，通常能更快命中并给出可解释证据
NUCLEI_CMS_TEMPLATE_DIRS = [
    os.path.join(NUCLEI_TEMPLATES_DIR, "misconfiguration"),
    os.path.join(NUCLEI_TEMPLATES_DIR, "exposed-panels"),
    os.path.join(NUCLEI_TEMPLATES_DIR, "exposures"),
    os.path.join(NUCLEI_TEMPLATES_DIR, "vulnerabilities"),
    os.path.join(NUCLEI_TEMPLATES_DIR, "default-logins"),
]

# AI 分析的条目上限（避免 analyzing 阶段因漏洞数量过多而过慢）
MAX_AI_VULN_PER_TASK = int(os.getenv("MAX_AI_VULN_PER_TASK", "30"))

# =========================
# DNSLog（用于 Log4j OAST 回连确认）
# =========================
DNSLOG_BASE_URL = os.getenv("DNSLOG_BASE_URL", "http://www.dnslog.cn")
# 需要你在浏览器登录 dnslog.cn 后复制 Cookie（通常包含 PHPSESSID）
DNSLOG_COOKIE = os.getenv("DNSLOG_COOKIE", "")

# =========================
# DVWA 登录态（可选，cookie-only 模式）
# =========================
# 用于 nuclei 模板直接携带 Cookie（绕过登录流程）。
# 格式示例（用于 Cookie header）：
#   DVWA_COOKIE=PHPSESSID=xxxxxx;security=low
DVWA_COOKIE = os.getenv("DVWA_COOKIE", "")

# OWASP ZAP 可执行文件路径（Windows 下可为 zap.exe 或 zap.bat）
# TODO: 替换为实际 ZAP 启动脚本路径
# ZAP 可执行文件路径（Windows安装版默认路径，根据实际安装路径调整）
ZAP_PATH = "C:\\Program Files\\ZAP\\Zed Attack Proxy\\zap.bat"


# =========================
# OWASP ZAP API 核心配置（新增！调用ZAP主动扫描必须）
# =========================
# ZAP API 访问地址（默认监听 127.0.0.1:8080，与ZAP界面配置一致）
ZAP_API_URL = os.getenv("ZAP_API_URL", "http://127.0.0.1:8080")
# ZAP API 密钥（替换为你ZAP界面里的实际密钥：96lr8a8ujkh6ncad62qemko3db）
ZAP_API_KEY = os.getenv("ZAP_API_KEY", "96lr8a8ujkh6ncad62qemko3db")
# ZAP 扫描超时时间（秒），避免扫描卡死
ZAP_SCAN_TIMEOUT = int(os.getenv("ZAP_SCAN_TIMEOUT", "300"))

# =========================
# CMS（YXCMS）登录配置（用于 ZAP 认证爬虫/主动扫描）
# =========================
YXCMS_ADMIN_USER = os.getenv("YXCMS_ADMIN_USER", "admin")
YXCMS_ADMIN_PASS = os.getenv("YXCMS_ADMIN_PASS", "123456")
YXCMS_LOGIN_PATH = os.getenv("YXCMS_LOGIN_PATH", "/index.php?r=admin/login/index")

# =========================
# 通义千问 API 配置
# =========================

# 通义千问 API Key，建议放在 .env 中，通过环境变量加载
# TODO: 在 .env 中配置 QWEN_API_KEY=<你的API密钥>
QWEN_API_KEY = os.getenv("QWEN_API_KEY", "sk-153d04274a6c48f9bc5eacce1abbb8b0")

# 通义千问 API 地址，不同产品线可能不同，请根据官方文档确认
# TODO: 如有变更，请根据实际接口地址修改
QWEN_API_URL = os.getenv(
    "QWEN_API_URL",
    "https://dashscope.aliyuncs.com/api/v1/services/aigc/text-generation/generation",
)

# 通义千问（DashScope）文本生成模型名称（必填）
# 你的 API 若可用，通常可用 "qwen-plus"。
QWEN_MODEL = os.getenv("QWEN_MODEL", "qwen-plus")


# =========================
# 公网扫描相关配置
# =========================

# 扫描模式：
# - auto  : 自动识别（本机 127.0.0.1:7777 走 dvwa，其他走 public）
# - public: 仅公网流程（CVE/通用模板，默认可启用 ZAP）
# - dvwa  : 仅 DVWA 流程（dvwa 模板 + cookie 登录链路）
SCAN_PROFILE = os.getenv("SCAN_PROFILE", "auto").strip().lower()

# 请求延迟（秒），用于控制对公网目标的访问频率，避免触发风控
# TODO: 根据实际授权与对方要求调整，建议 >= 1 秒
REQUEST_DELAY = float(os.getenv("REQUEST_DELAY", "1.0"))

# User-Agent 池，用于对公网目标发起请求时随机选择，增强模拟真实流量
# TODO: 可在此补充更多常见浏览器 UA
USER_AGENT_POOL = [
    # 常见 Windows Chrome UA 示例
    (
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) "
        "AppleWebKit/537.36 (KHTML, like Gecko) "
        "Chrome/120.0.0.0 Safari/537.36"
    ),
    # 常见 Windows Edge UA 示例
    (
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) "
        "AppleWebKit/537.36 (KHTML, like Gecko) "
        "Chrome/120.0.0.0 Safari/537.36 Edg/120.0.0.0"
    ),
    # TODO: 根据需要增加更多 User-Agent
]

# 代理列表，用于公网扫描时通过代理转发（如使用 Burp / 代理池 等）
# 代理格式示例："http://127.0.0.1:8080"
# TODO: 根据实际代理环境配置，如无代理可保持为空列表
PROXY_LIST = [
    # "http://127.0.0.1:8080",
]


# =========================
# 数据库配置
# =========================

# 使用 SQLite 数据库存储扫描任务与结果
# 默认将数据库文件放在项目 data 目录下
DATA_DIR = os.path.join(BASE_DIR, "data")
if not os.path.exists(DATA_DIR):
    os.makedirs(DATA_DIR, exist_ok=True)

# 数据库 URI，SQLAlchemy 使用的连接字符串
# TODO: 如需使用 MySQL/PostgreSQL，请修改为对应的连接串
DATABASE_URI = os.getenv(
    "DATABASE_URI",
    "sqlite:///" + os.path.join(DATA_DIR, "scanner.db"),
)


# =========================
# 日志配置
# =========================

# 日志级别，支持 "DEBUG" / "INFO" / "WARNING" / "ERROR" / "CRITICAL"
# TODO: 开发环境可使用 DEBUG，生产建议使用 INFO 或更高
LOG_LEVEL = os.getenv("LOG_LEVEL", "INFO")

# 日志目录，所有日志文件将写入此目录
LOG_PATH = os.path.join(BASE_DIR, "logs")
if not os.path.exists(LOG_PATH):
    os.makedirs(LOG_PATH, exist_ok=True)


# =========================
# 其他全局配置（预留）
# =========================

# Flask 调试模式开关
DEBUG = os.getenv("FLASK_DEBUG", "False").lower() == "true"

# 默认绑定地址与端口（用于开发调试）
HOST = os.getenv("FLASK_HOST", "127.0.0.1")
PORT = int(os.getenv("FLASK_PORT", "5000"))

