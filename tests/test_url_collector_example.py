"""
URL 收集模块示例测试。

注意：
- 该测试依赖本地已正确安装并配置好的 gau.exe；
- 若 GAU_PATH 指向的路径不存在，则自动跳过测试；
- 实际环境可使用 BUUCTF 等授权靶场域名进行验证。
"""

from pathlib import Path

import pytest

from config.settings import GAU_PATH
from core.url_collector import GauCollector


@pytest.mark.skipif(
    not Path(GAU_PATH).exists(),
    reason="GAU_PATH 指向的 gau.exe 不存在，跳过 URL 收集测试",
)
def test_gau_collect_buuctf_example() -> None:
    """
    使用 BUUCTF 域名演示 URL 收集流程。

    仅验证流程是否可正常执行，并不对具体 URL 内容做强约束。
    """
    collector = GauCollector("buuctf.cn")
    ok, urls, msg = collector.collect_and_filter(verify_http=False)

    assert ok is True, f"URL 收集失败: {msg}"
    assert isinstance(urls, list)
    # 不强制要求一定有数据，但若有则应为字符串
    if urls:
        assert isinstance(urls[0], str)

