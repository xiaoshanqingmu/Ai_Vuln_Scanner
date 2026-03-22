"""
AI 深度分析模块示例测试。

说明：
- 即使未配置 QWEN_API_KEY/QWEN_API_URL，本测试也应能通过（走降级逻辑）；
- 仅验证函数调用流程与数据库写入是否正常，不依赖真实通义千问服务。
"""

from app import create_app
from core.ai_analyzer import analyze_attack_chain, analyze_vulnerability
from database import (
    add_vulnerability,
    add_scan_task,
    get_scan_task_by_id,
    init_db,
)


def test_ai_analyzer_fallback_flow() -> None:
    """
    1. 创建任务与漏洞记录；
    2. 调用 analyze_vulnerability（预期可在无 QWEN 配置时走降级流程）；
    3. 调用 analyze_attack_chain，验证不会抛异常。
    """
    app = create_app()
    with app.app_context():
        init_db(app)

        # 创建扫描任务
        ok, task, _ = add_scan_task(task_id="ai-test-task-1", domain="buuctf.cn:8080")
        assert ok and task is not None
        assert get_scan_task_by_id(task.id) is not None

        # 创建漏洞记录
        ok, vuln, _ = add_vulnerability(
            task_id=task.task_id,
            tool="nuclei",
            vuln_name="Test SQL Injection",
            risk_level="high",
            url="http://buuctf.cn:8080/vuln.php?id=1",
            param="id",
            description="Possible SQL injection vulnerability.",
        )
        assert ok and vuln is not None

        # 单条漏洞 AI 分析
        ok, msg = analyze_vulnerability(vuln_id=vuln.id, vuln_data=vuln)
        assert isinstance(ok, bool)
        assert isinstance(msg, str)

        # 攻击链分析
        ok, msg = analyze_attack_chain(task_id=task.task_id)
        assert isinstance(ok, bool)
        assert isinstance(msg, str)

