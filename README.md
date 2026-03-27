# AI Vuln Scanner

一个面向教学靶场与公网目标的 Web 漏洞扫描与报告系统，采用 **Flask + Celery + SQLAlchemy + Nuclei + ZAP + AI 分析** 的组合架构，支持：

- 任务化扫描（创建任务、轮询状态、下载报告）
- 多模式扫描（`public` / `dvwa` / `cms`）
- URL 收集、规则扫描、主动扫描、源码对齐探测
- AI 漏洞解释与修复建议
- Excel/PDF 报告导出

> 仅用于授权测试和教学实验。请确保你对目标拥有合法授权。

---

## 1. 项目目标与设计思路

本项目不是“单一引擎扫描器”，而是一个可扩展的扫描流水线：

1. `task_api` 接收目标并规范化
2. `async_task` 编排任务状态、收集 URL、调度扫描
3. `vuln_scanner` 执行双引擎（Nuclei + ZAP）与源码对齐探测
4. `db_operation` 去重入库
5. `report_generator` 做 AI 分析并输出报告

这样实现的核心原因：

- **提高命中率**：Nuclei 擅长模板化快速匹配，ZAP 擅长交互型/动态漏洞
- **提高可信度**：加入 `source-probe`，让 DVWA/YXCMS 结果尽量贴近源码漏洞点
- **提高稳定性**：关键节点有降级逻辑（无 Redis、无 gau、无 ZAP、PDF 依赖缺失）
- **提高可运营性**：任务状态可观测，结果落库可追踪，报告可重复导出

---

## 2. 技术栈与作用

- `Flask`：提供 Web/API 服务层
- `Celery + Redis`：异步任务队列（不可用时自动降级线程同步）
- `SQLAlchemy + SQLite`：任务/漏洞持久化与去重
- `gau`：URL 收集（失败时自动回退基础 URL）
- `nuclei`：模板扫描（支持场景化模板加载）
- `OWASP ZAP API`：主动扫描与动态探测
- `DashScope/Qwen`：AI 漏洞分析、PoC 思路、修复建议、攻击链评估
- `openpyxl` / `weasyprint`：Excel/PDF 报告生成

---

## 3. 架构与执行链路（原理）

### 3.1 高层流程

`POST /api/tasks` -> 创建任务 -> 进入 `pending`

`run_full_scan/run_full_scan_sync`：

1. 状态切到 `scanning`
2. 判断扫描模式（`_resolve_scan_profile`）
3. 本地靶场可达性预检（DVWA/CMS）
4. URL 收集（`GauCollector.collect_and_filter`）
5. 漏洞扫描（`scan_urls`）
   - Nuclei
   - ZAP
   - Source-Probe（DVWA/YXCMS）
6. 状态切到 `analyzing`
7. AI 分析 + 报告生成（Excel 必出，PDF 尽力）
8. 状态切到 `success` 或 `failed`

### 3.2 扫描模式策略

- `public`：偏通用模板与公网流程，兼顾覆盖与稳定性
- `dvwa`：自动登录+`security=low`，加载 `dvwa-*.yaml`，保留 DVWA 对齐结果
- `cms`：优先 ZAP，补充 CMS 模板与 YXCMS 源码探测，减少噪音告警

选择这种“模式化”实现的原因：

- 你之前遇到的核心问题就是“同一套流程扫所有目标”，导致模板噪音、速度慢、命中偏差
- 模式化能把“检测逻辑”和“目标类型”对齐

### 3.3 可靠性设计

- Celery 不可用 -> 自动后台线程执行，不阻断业务
- gau 不可用 -> 使用基础 URL 降级
- ZAP 不可用 -> 记录为部分模块降级，不让任务整体失败
- WeasyPrint 依赖缺失 -> PDF 失败不影响 Excel
- watchdog 防卡死 -> `scanning` 超时自动失败

---

## 4. 目录与关键文件

- `app.py`：Flask 启动入口
- `app/__init__.py`：应用工厂、数据库与蓝图注册、Celery 实例
- `app/api/task_api.py`：任务创建、目标规范化、状态查询
- `app/api/report_api.py`：报告下载接口（不存在时自动生成）
- `core/async_task.py`：全流程任务编排、模式识别、预检、报告触发
- `core/url_collector.py`：gau 收集与 URL 过滤
- `core/vuln_scanner.py`：Nuclei/ZAP/source-probe 主逻辑
- `core/report_generator.py`：AI 分析与 Excel/PDF 报告
- `config/settings.py`：工具路径、扫描模式、API key、数据库、日志等配置
- `database/db_operation.py`：任务和漏洞入库、去重、AI 字段更新

---

## 5. 启动前准备（一次性）

### 5.1 Python 环境

```powershell
Set-Location d:\Ai_Vuln_Scanner
python -m venv .venv
.\.venv\Scripts\Activate.ps1
pip install -r requirements.txt
```

### 5.2 外部工具

确保以下文件/程序可用：

- `tools\nuclei.exe`
- `tools\gau.exe`（推荐）
- OWASP ZAP（GUI 或 daemon）
- Redis（推荐，用于 Celery 异步）

### 5.3 环境配置（推荐用 `.env`）

重点变量：

- `SCAN_PROFILE=auto`
- `ZAP_API_URL=http://127.0.0.1:8080`
- `ZAP_API_KEY=<你的key>`
- `DVWA_COOKIE=`（可空，系统会优先自动登录获取）
- `YXCMS_ADMIN_USER=admin`
- `YXCMS_ADMIN_PASS=123456`
- `QWEN_API_KEY=<你的key>`
- `QWEN_MODEL=qwen-plus`

> 安全建议：不要在 `settings.py` 里硬编码真实密钥，统一放 `.env`。

---

## 6. 每次启动步骤（按顺序）

### 6.1 启动 Redis（可选但推荐）

确认本机 `6379` 可用。

### 6.2 启动 Celery Worker（推荐）

```powershell
Set-Location d:\Ai_Vuln_Scanner
.\.venv\Scripts\Activate.ps1
celery -A app.celery_app worker --loglevel=INFO -P solo
```

### 6.3 启动 Flask 服务

```powershell
Set-Location d:\Ai_Vuln_Scanner
.\.venv\Scripts\Activate.ps1
python app.py
```

访问：`http://127.0.0.1:5000`

### 6.4 启动 ZAP（推荐 daemon）

```powershell
& "C:\Program Files\ZAP\Zed Attack Proxy\zap.bat" `
  -daemon -host 127.0.0.1 -port 8080 `
  -config api.key=你的key `
  -config api.addrs.addr.name=127.0.0.1 `
  -config api.addrs.addr.regex=true
```

连通性验证：

```powershell
Invoke-RestMethod -Uri "http://127.0.0.1:8080/JSON/core/view/version/?apikey=你的key"
```

---

## 7. 扫描操作（逐步示例）

### 7.1 创建任务

```powershell
$body = '{"domain":"http://127.0.0.1:7777/index.php","user_confirmed":true}'
Invoke-RestMethod -Uri "http://127.0.0.1:5000/api/tasks" -Method POST -ContentType "application/json" -Body $body
```

返回中拿 `task_id`。

### 7.2 轮询状态

```powershell
$id = "替换成你的task_id"
Invoke-RestMethod -Uri "http://127.0.0.1:5000/api/tasks/$id/status" -Method GET
```

状态含义：

- `pending`：已入队
- `scanning`：URL 收集与漏洞扫描
- `analyzing`：AI 分析和报告生成
- `success`：完成
- `failed`：失败（查日志排障）

### 7.3 下载报告

- Excel：`GET /api/download/excel/<task_id>`
- PDF：`GET /api/download/pdf/<task_id>`

默认输出目录：

- `reports/excel`
- `reports/pdf`

---

## 8. 三类目标的实战建议

### 8.1 DVWA（本地靶场）

前置：

- 登录 `admin/password`
- `DVWA Security` 设为 `Low`
- 确认 `login.php` 与 `vulnerabilities/*` 可访问

建议：

- 扫站点根或 `index.php`，不要直接只扫深层模块 URL
- 保持 ZAP 开启，DOM/交互型更依赖 ZAP

### 8.2 YXCMS（本地 CMS 靶场）

前置：

- 确认后台登录地址可访问
- `.env` 设置 `YXCMS_ADMIN_USER/PASS`

特点：

- 系统会补充后台常见入口 URL
- 优先跑 ZAP，再按需补 Nuclei
- 附加源码探测（会话固定、XSS 面、跳转、路径读取等）

### 8.3 公网 URL

原则：

- 必须有授权
- 建议使用 `SCAN_PROFILE=public`
- 控制 `REQUEST_DELAY` 降低目标压力

实践建议：

- 公网更依赖通用模板与 ZAP 基础扫描
- 对高价值资产分批扫描，减少一次性 URL 规模

---

## 9. 为什么这样实现（关键取舍）

### 9.1 为什么保留数据库而不是“直接出报告”

- 任务状态需要持久化（可轮询、可追溯）
- 扫描结果需要去重（避免 AI 重复分析和报告噪音）
- 报告可重复导出，不必重扫
- 便于后续做趋势统计与历史对比

### 9.2 为什么 Nuclei + ZAP 双引擎

- Nuclei：模板化快、可批量、可复现
- ZAP：对动态页面、会话、主动探测更强
- 双引擎互补降低“漏报”与“单点失效”

### 9.3 为什么做模式分流

- DVWA/YXCMS 是“已知结构”靶场，适合定向规则与源码探测
- 公网是“未知结构”目标，适合通用模板和稳健策略
- 统一流程无法同时兼顾速度、准确率和噪音控制

---

## 10. 当前优点、缺点与改进方向

### 10.1 优点

- 工程链路完整：任务、扫描、AI、报告闭环
- 兼容性好：组件缺失时可降级运行
- 本地靶场覆盖强化：DVWA/YXCMS 源码对齐探测
- 去重入库：减少重复漏洞对 AI 与报告性能影响

### 10.2 缺点（客观）

- 规则与脚本仍偏“靶场导向”，面对复杂真实业务场景覆盖仍有限
- ZAP API 调度为串行策略，扫描时长受限于目标规模
- `settings.py` 仍存在默认敏感配置示例，安全边界需进一步收敛
- 目前缺少更完善的测试集与回归基线（按目标类型分层）

### 10.3 可执行改进路线

1. **安全配置治理**：移除代码中默认密钥，仅允许 `.env` 注入；增加启动时敏感配置自检  
2. **插件化扫描策略**：把 `dvwa/cms/public` 进一步抽象成策略插件，降低主流程耦合  
3. **并行调度优化**：分段并发 ZAP/Nuclei，增加任务级超时与重试策略矩阵  
4. **结果置信评分**：为不同来源（nuclei/zap/source-probe）建立证据评分与融合规则  
5. **测试体系建设**：增加单元测试 + 集成测试 + 靶场回归基线（固定数据集）  
6. **报告产品化**：支持同一任务多模板导出（教学版/运维版/管理层摘要版）

---

## 11. 常见问题排查（实战版）

### 11.1 任务卡在 `scanning`

优先检查：

- 目标是否可访问（本地端口是否启动）
- `nuclei.exe`、`gau.exe` 是否存在且可执行
- ZAP 是否可访问 API
- 是否被防火墙/杀软拦截

### 11.2 结果为 0 漏洞

常见原因：

- 靶场未登录或安全等级不对（DVWA）
- 只扫了深层页面，未包含站点根
- 模式识别不匹配目标（建议 `SCAN_PROFILE=auto`）
- ZAP 未启动导致交互型漏洞覆盖不足

### 11.3 PDF 导出失败

原因通常是 WeasyPrint 的系统依赖未安装。  
处理方式：先下载 Excel；后续补齐系统依赖再导 PDF。

---

## 12. API 快速索引

- `POST /api/tasks`：创建扫描任务
- `GET /api/tasks/<task_id>/status`：查询任务状态
- `GET /api/download/excel/<task_id>`：下载 Excel 报告
- `GET /api/download/pdf/<task_id>`：下载 PDF 报告

---

## 13. 开发与质量建议

建议在提交前执行：

```powershell
ruff check .
black .
pytest
```

并重点关注：

- 新增规则是否有误报回归
- 扫描模式分流是否影响既有目标
- AI 分析失败时是否仍能稳定导出报告

---

## 14. 合规声明

本项目仅用于教学、科研和授权安全测试。  
未经授权请勿扫描第三方系统。使用者需自行承担合规责任。

