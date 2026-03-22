# AI Vuln Scanner（本机 DVWA 联调使用手册）

本手册面向 Windows（PowerShell）环境，目标是把 **DVWA 本地靶场** 与本项目打通，跑通：
**创建任务 → 扫描 → 结果入库 → 导出 Excel 报告**。

> 你当前的 DVWA 示例地址：`http://127.0.0.1:7777/index.php`

---

## 0. 你需要启动哪些东西（总览）

### 必需（最低可用链路）
- **DVWA 靶场**：例如 `http://127.0.0.1:7777/`
- **本项目 Flask 服务**：提供前端/API，默认 `http://127.0.0.1:5000`
- **nuclei.exe**：本项目会调用它做规则扫描（HTTP 类漏洞）
- （可选但推荐）**gau.exe**：用于 URL 收集；没有也能降级运行

### 强烈推荐（提升覆盖与稳定性）
- **Redis + Celery Worker**：让扫描异步化、并发更稳（否则会走后台线程降级）
- **OWASP ZAP（桌面版或 Docker）**：覆盖“需要登录/爬虫/JS/Dom XSS”等交互型漏洞点

### 现状提示（你机器日志里已经出现过）
- **ZAP 未启动**：会被项目自动降级跳过
- **WeasyPrint 系统依赖缺失**：PDF 报告会失败，但 Excel 不受影响

---

## 1. 一次性准备（只做一遍）

### 1.1 Python 依赖

在项目根目录执行：

```powershell
Set-Location d:\Ai_Vuln_Scanner
python -m venv .venv
.\.venv\Scripts\Activate.ps1
pip install -r requirements.txt
```

### 1.2 工具文件放置（nuclei / gau）

确保以下文件存在：
- `d:\Ai_Vuln_Scanner\tools\nuclei.exe`
- `d:\Ai_Vuln_Scanner\tools\gau.exe`（可选）

并确认 `config/settings.py` 中路径指向项目内工具：
- `NUCLEI_PATH = os.path.join(BASE_DIR, "tools", "nuclei.exe")`
- `GAU_PATH = os.path.join(BASE_DIR, "tools", "gau.exe")`

### 1.3 DVWA 前置设置（强烈建议）

为了让扫描能稳定命中 DVWA 的漏洞点：
- **用浏览器登录 DVWA**（默认用户通常为 `admin/password`）
- 进入 `DVWA Security`，把安全等级设为 **Low**
- 确保能访问这些页面（示例）：
  - `http://127.0.0.1:7777/login.php`
  - `http://127.0.0.1:7777/vulnerabilities/xss_r/`
  - `http://127.0.0.1:7777/vulnerabilities/sqli/`

> 说明：像 `xss_d`（DOM XSS）依赖浏览器执行 JS，单靠 nuclei（HTTP 响应匹配）覆盖不到，需 ZAP/浏览器类引擎。

---

## 2. 每次使用怎么启动（按顺序照做）

### 2.1（可选）启动 Redis

如果你安装了 Redis（本机或 Docker），先启动它并确保端口可用（默认 6379）。

### 2.2（可选）启动 Celery Worker

在项目根目录打开一个新的 PowerShell：

```powershell
Set-Location d:\Ai_Vuln_Scanner
.\.venv\Scripts\Activate.ps1
celery -A app.celery_app worker --loglevel=INFO -P solo
```

> Windows 建议 `-P solo`。

### 2.3 启动本项目 Flask 服务

再开一个 PowerShell：

```powershell
Set-Location d:\Ai_Vuln_Scanner
.\.venv\Scripts\Activate.ps1
python app.py
```

启动后应能访问：
- `http://127.0.0.1:5000`

### 2.4（推荐）启动 ZAP

你可以用桌面版启动，也可以用 daemon 模式启动（推荐 daemon，便于自动化）。

#### 方式 A：ZAP GUI（最简单）
- 打开 ZAP
- 打开 API（或保持默认开启）
- 确保监听：`http://127.0.0.1:8080`
- 将 API Key 填到 `.env` 或 `config/settings.py` 的 `ZAP_API_KEY`

#### 方式 B：ZAP Daemon（推荐，一条命令）
在 PowerShell 执行（路径按你实际安装调整；项目默认示例见 `config/settings.py` 的 `ZAP_PATH`）：

```powershell
& "C:\Program Files\ZAP\Zed Attack Proxy\zap.bat" `
  -daemon -host 127.0.0.1 -port 8080 `
  -config api.key=96lr8a8ujkh6ncad62qemko3db `
  -config api.addrs.addr.name=127.0.0.1 `
  -config api.addrs.addr.regex=true
```

#### 验证 ZAP 已启动（必须）
浏览器或 PowerShell 访问：

```powershell
Invoke-RestMethod -Uri "http://127.0.0.1:8080/JSON/core/view/version/?apikey=96lr8a8ujkh6ncad62qemko3db"
```

能返回版本号才算 OK。

---

## 3. 创建 DVWA 扫描任务（PowerShell）

### 3.1 扫 DVWA 站点根（验证链路）

```powershell
$body = '{"domain":"http://127.0.0.1:7777/index.php","user_confirmed":true}'
Invoke-RestMethod -Uri "http://127.0.0.1:5000/api/tasks" -Method POST -ContentType "application/json" -Body $body
```

返回里会有 `task_id`。

### 3.2 轮询任务状态

```powershell
$id="替换为你的task_id"
Invoke-RestMethod -Uri "http://127.0.0.1:5000/api/tasks/$id/status" -Method GET
```

状态流转通常为：
- `pending` → `scanning` → `analyzing` → `success/failed`

---

## 4. 报告输出在哪里

- **Excel**：`d:\Ai_Vuln_Scanner\reports\excel\*.xlsx`
- **PDF**：`d:\Ai_Vuln_Scanner\reports\pdf\*.pdf`

> Windows 上若缺 WeasyPrint 依赖，PDF 可能失败，但 Excel 仍会生成。

---

## 5. 常见问题排查

### 5.1 “只出一个漏洞/出洞很少”

常见原因：
- **ZAP 没启动**：DOM XSS、需要交互/爬虫的模块覆盖不到
- **DVWA 安全等级不是 Low**
- **需要登录的模块**：如果扫描引擎没做认证流程，就只能扫到未授权页面

### 5.2 “任务卡在 scanning”

项目内置 watchdog（超时会失败），但你仍应优先检查：
- `gau.exe`/`nuclei.exe` 是否能运行
- 是否被代理/杀软拦截
- 目标站是否可访问

---

## 6. DVWA 规则/模板怎么扩展

### 6.1 nuclei（HTTP 可验证的漏洞点）

把自定义模板放到：
- `d:\Ai_Vuln_Scanner\tools\nuclei-templates\custom\`

并在 `config/settings.py` 的 `NUCLEI_TARGETED_TEMPLATES` 中加入路径。

### 6.2 ZAP（认证 + 爬虫 + JS/DOM）

需要：
- 配置 ZAP API
- 配置认证上下文（登录表单、会话保持）
- 启用 spider / ajax spider / active scan

（后续我会把这部分直接接到项目扫描流程里，让你“一键扫 DVWA 多模块”。）

