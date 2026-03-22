import logging
from typing import Any, Dict

from flask import Blueprint, jsonify, request

from config.settings import STRICT_COMPLIANCE_MODE
from core.compliance import (
    check_authorized_domain,
    record_scan_audit,
    verify_user_confirm,
)

logger = logging.getLogger("compliance")

compliance_bp = Blueprint("compliance_api", __name__, url_prefix="/api/compliance")


def _json_error(message: str, code: str, http_status: int = 400) -> Any:
    """
    统一 JSON 错误返回格式。

    :param message: 错误提示信息
    :param code: 业务错误码
    :param http_status: HTTP 状态码
    :return: Flask Response 对象
    """
    payload: Dict[str, Any] = {
        "success": False,
        "error": {
            "message": message,
            "code": code,
        },
    }
    return jsonify(payload), http_status


@compliance_bp.route("/check", methods=["POST"])
def compliance_check() -> Any:
    """
    合规校验接口。

    前端在创建扫描任务前应调用本接口，后端会再次进行合规校验，
    即使前端被绕过或篡改，本接口仍可拦截未授权目标。

    请求 JSON 参数：
    - task_id: 扫描任务 ID
    - domain: 目标地址（公网域名/IP，可携带端口）
    - user_confirmed: 前端用户是否勾选授权确认复选框（布尔值）

    返回 JSON：
    - success: 是否校验通过
    - data: 校验说明信息（在 success=True 时返回）
    - error: 错误信息（在 success=False 时返回）
    """
    try:
        data = request.get_json(silent=True) or {}
        task_id = (data.get("task_id") or "").strip()
        domain = (data.get("domain") or "").strip()
        user_confirmed = bool(data.get("user_confirmed"))

        if not task_id or not domain:
            logger.warning(
                "合规校验参数错误: task_id=%s domain=%s user_confirmed=%s",
                task_id,
                domain,
                user_confirmed,
            )
            return _json_error("参数错误：task_id 与 domain 均不能为空", "INVALID_PARAMS", 400)

        # 记录审计日志（无论是否通过）
        record_scan_audit(task_id=task_id, domain=domain, user_confirmed=user_confirmed)

        # 白名单校验
        authorized, auth_msg = check_authorized_domain(domain)

        if not authorized and STRICT_COMPLIANCE_MODE:
            logger.warning(
                "严格模式下白名单校验未通过，拒绝扫描: task_id=%s domain=%s",
                task_id,
                domain,
            )
            return _json_error(
                f"严格模式下目标未授权：{auth_msg}",
                "UNAUTHORIZED_TARGET",
                403,
            )

        # 授权确认校验
        if not verify_user_confirm(task_id):
            logger.warning(
                "严格模式下用户未完成授权确认，拒绝扫描: task_id=%s domain=%s",
                task_id,
                domain,
            )
            return _json_error(
                "未完成授权确认，已拒绝本次扫描请求",
                "UNCONFIRMED_AUTH",
                403,
            )

        # 非严格模式下若未授权，仅返回警告信息但仍允许
        if not authorized and not STRICT_COMPLIANCE_MODE:
            logger.info(
                "非严格模式下允许未在白名单中的目标: task_id=%s domain=%s",
                task_id,
                domain,
            )
            return jsonify(
                {
                    "success": True,
                    "data": {
                        "message": f"非严格模式下允许目标：{auth_msg}",
                        "strict_mode": False,
                        "authorized": False,
                    },
                }
            )

        # 正常通过
        logger.info(
            "合规校验通过: task_id=%s domain=%s strict=%s",
            task_id,
            domain,
            STRICT_COMPLIANCE_MODE,
        )
        return jsonify(
            {
                "success": True,
                "data": {
                    "message": "合规校验通过，允许执行扫描任务",
                    "strict_mode": STRICT_COMPLIANCE_MODE,
                    "authorized": authorized,
                },
            }
        )
    except Exception as exc:  # noqa: BLE001
        logger.exception("合规校验接口异常: 错误=%s", exc)
        return _json_error("服务器内部错误，请稍后重试", "INTERNAL_ERROR", 500)


__all__ = ["compliance_bp"]

