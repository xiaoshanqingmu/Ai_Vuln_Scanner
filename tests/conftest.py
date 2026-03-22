"""
Pytest 全局配置。

确保在运行测试时可以正确导入项目根目录下的包，例如 app、config、database 等。
"""

import sys
from pathlib import Path


PROJECT_ROOT = Path(__file__).resolve().parents[1]

if str(PROJECT_ROOT) not in sys.path:
    sys.path.insert(0, str(PROJECT_ROOT))

