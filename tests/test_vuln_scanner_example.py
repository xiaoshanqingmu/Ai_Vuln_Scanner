"""
漏洞扫描模块示例（默认跳过）。

原因：
- ZAP 需要你本机启动服务；
- nuclei 需要你本机安装 nuclei.exe 并配置 NUCLEI_PATH；
- 公网扫描务必仅对“已授权靶场”进行。
"""

from pathlib import Path

import pytest

from config.settings import NUCLEI_PATH
from core.vuln_scanner import scan_urls


@pytest.mark.skipif(
    not Path(NUCLEI_PATH).exists(),
    reason="NUCLEI_PATH 指向的 nuclei.exe 不存在，跳过扫描示例",
)
def test_vuln_scan_dvwa_example() -> None:
    """
    DVWA 示例（请确保目标为你已授权的 DVWA 环境）。

    该测试只验证扫描入口函数可以运行，不对漏洞数量做强约束。
    """
    task_id = "test-task-dvwa-001"
    urls = [
        "http://127.0.0.1:8080/",  # 建议替换为你本地/授权靶场的 DVWA 地址
    ]

    ok, summary, msg = scan_urls(task_id=task_id, urls=urls)
    assert isinstance(ok, bool)
    assert isinstance(summary, dict)
    assert isinstance(msg, str)

