from flask import Flask

from app import create_app
from app.api.compliance_api import compliance_bp
from core import compliance
from config import settings


def _create_test_app() -> Flask:
    """
    创建用于测试的 Flask 应用，注册合规接口蓝图。
    """
    app = create_app()
    app.register_blueprint(compliance_bp)
    return app


def test_compliance_check_authorized_domain() -> None:
    """
    测试合规校验接口在授权目标上的行为。
    """
    # 确保测试时有一个授权白名单示例
    settings.AUTHORIZED_DOMAINS.clear()
    settings.AUTHORIZED_DOMAINS.extend(["*.buuctf.cn", "buuctf.cn:8080"])

    app = _create_test_app()
    client = app.test_client()

    # 模拟前端勾选授权确认
    # 通过调用接口记录审计而不是直接操作内部映射

    payload = {
        "task_id": "test-task-compliance-1",
        "domain": "buuctf.cn:8080",
        "user_confirmed": True,
    }
    response = client.post("/api/compliance/check", json=payload)
    assert response.status_code == 200
    data = response.get_json()
    assert data["success"] is True
    assert "合规校验通过" in data["data"]["message"]


def test_compliance_check_unauthorized_strict_mode() -> None:
    """
    测试在严格模式下，未授权目标会被拒绝。
    """
    settings.AUTHORIZED_DOMAINS.clear()
    settings.AUTHORIZED_DOMAINS.extend(["*.buuctf.cn"])
    settings.STRICT_COMPLIANCE_MODE = True

    app = _create_test_app()
    client = app.test_client()

    payload = {
        "task_id": "test-task-compliance-2",
        "domain": "example.com",
        "user_confirmed": True,
    }
    response = client.post("/api/compliance/check", json=payload)
    assert response.status_code == 403
    data = response.get_json()
    assert data["success"] is False
    assert data["error"]["code"] == "UNAUTHORIZED_TARGET"

