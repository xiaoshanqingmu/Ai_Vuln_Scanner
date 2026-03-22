"""
数据库操作示例与基础测试。

可以通过以下命令运行：

    cd /d d:\Ai_Vuln_Scanner
    venv\Scripts\activate
    pytest -q
"""

from app import create_app
from database import (
    init_db,
    add_scan_task,
    get_scan_task_by_id,
    update_scan_task_status,
    add_vulnerability,
    get_vulnerabilities_by_task_id,
    update_vulnerability_ai_info,
)


def test_database_operations_example() -> None:
    """
    演示数据库基础操作流程：
    1. 初始化应用与数据库
    2. 新增扫描任务
    3. 更新任务状态
    4. 新增漏洞记录
    5. 更新漏洞 AI 分析字段
    """
    app = create_app()
    with app.app_context():
        # 初始化数据库（如表不存在会自动创建）
        init_db(app)

        # 新增扫描任务
        ok, task, _ = add_scan_task(task_id="test-task-123", domain="buuctf.cn:8080")
        assert ok is True
        assert task is not None

        # 通过 ID 查询任务
        same_task = get_scan_task_by_id(task.id)
        assert same_task is not None
        assert same_task.task_id == "test-task-123"

        # 更新任务状态
        ok, updated_task, _ = update_scan_task_status(task.id, "finished")
        assert ok is True
        assert updated_task is not None
        assert updated_task.status == "finished"

        # 新增漏洞记录
        ok, vuln, _ = add_vulnerability(
            task_id=task.task_id,
            tool="nuclei",
            vuln_name="Test Vulnerability",
            risk_level="high",
            url="http://buuctf.cn:8080/test",
            param="id",
            description="This is a test vulnerability.",
        )
        assert ok is True
        assert vuln is not None

        # 根据任务 ID 获取漏洞列表
        vulns = get_vulnerabilities_by_task_id(task.task_id)
        assert len(vulns) >= 1

        # 更新漏洞 AI 分析信息
        ok, updated_vuln, _ = update_vulnerability_ai_info(
            vulnerability_id=vuln.id,
            attack_principle="测试攻击原理",
            poc_suggestion="测试 PoC 建议",
            custom_fix="测试修复建议",
            attack_chain_risk="测试攻击链风险",
        )
        assert ok is True
        assert updated_vuln is not None
        assert updated_vuln.attack_principle == "测试攻击原理"

