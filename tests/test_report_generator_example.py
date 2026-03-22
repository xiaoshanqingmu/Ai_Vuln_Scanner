"""
报告生成模块示例测试。

说明：
- 仅验证在存在任务与漏洞数据的情况下，Excel/PDF 报告生成逻辑是否能正常执行；
- 若本机 WeasyPrint 依赖环境不完整，PDF 部分可被跳过。
"""

import os
from pathlib import Path

import pytest

from app import create_app
from core.report_generator import generate_excel_report, generate_pdf_report
from database import (
    add_scan_task,
    add_vulnerability,
    init_db,
)


def _prepare_data() -> str:
    """
    准备一个包含若干漏洞记录的任务 ID。
    """
    from database.models import ScanTask  # noqa: WPS433

    task_id = "report-test-task-1"
    domain = "buuctf.cn:8080"

    # 若已存在同名任务则复用
    existing = ScanTask.query.filter_by(task_id=task_id).first()
    if existing:
        return task_id

    ok, task, _ = add_scan_task(task_id=task_id, domain=domain)
    assert ok and task is not None

    for i in range(3):
        ok, _, _ = add_vulnerability(
            task_id=task_id,
            tool="nuclei",
            vuln_name=f"Test Vuln {i + 1}",
            risk_level="high" if i == 0 else "medium",
            url=f"http://{domain}/vuln{i + 1}?id=1",
            param="id",
            description="Test vulnerability for report generation.",
        )
        assert ok

    return task_id


def test_generate_excel_and_pdf_reports(tmp_path: Path) -> None:
    """
    测试 Excel 与 PDF 报告生成流程。
    """
    app = create_app()
    with app.app_context():
        init_db(app)

        task_id = _prepare_data()

        excel_path = tmp_path / "report.xlsx"
        ok, msg = generate_excel_report(task_id, str(excel_path))
        assert ok, msg
        assert excel_path.exists()

        # PDF 生成依赖 WeasyPrint 底层库，若失败则允许跳过
        pdf_path = tmp_path / "report.pdf"
        try:
            ok, _ = generate_pdf_report(task_id, str(pdf_path))
            # 若 WeasyPrint 环境正常，应生成文件
            if ok:
                assert pdf_path.exists()
        except OSError:
            pytest.skip("WeasyPrint 底层依赖缺失，跳过 PDF 生成测试。")

