import logging
import os
from typing import Any

from flask import Blueprint, Response, jsonify, send_file

from config.settings import BASE_DIR
from core.report_generator import generate_excel_report, generate_pdf_report
from database.models import ScanTask

logger = logging.getLogger("report")

report_bp = Blueprint("report_api", __name__, url_prefix="/api/download")


def _build_report_paths(task_id: str) -> tuple[str, str] | tuple[None, None]:
    """
    根据 task_id 与任务信息构造 Excel/PDF 报告路径。

    报告文件名格式为：“渗透测试报告_<task_id>_<domain>.(xlsx|pdf)”。
    """
    task = ScanTask.query.filter_by(task_id=task_id).first()
    if not task:
        return None, None

    safe_domain = str(task.domain).replace(":", "_").replace("/", "_")
    filename_base = f"渗透测试报告_{task.task_id}_{safe_domain}"

    excel_dir = os.path.join(BASE_DIR, "reports", "excel")
    pdf_dir = os.path.join(BASE_DIR, "reports", "pdf")

    excel_path = os.path.join(excel_dir, f"{filename_base}.xlsx")
    pdf_path = os.path.join(pdf_dir, f"{filename_base}.pdf")
    return excel_path, pdf_path


@report_bp.route("/excel/<string:task_id>", methods=["GET"])
def download_excel(task_id: str) -> Any:
    """
    Excel 报告下载接口。

    若报告不存在，则先生成后再返回给前端。
    """
    excel_path, _ = _build_report_paths(task_id)
    if not excel_path:
        return jsonify({"success": False, "error": {"message": "任务不存在。"}}), 404

    if not os.path.exists(excel_path):
        ok, msg = generate_excel_report(task_id, excel_path)
        if not ok:
            return jsonify({"success": False, "error": {"message": msg}}), 500

    try:
        file_size = os.path.getsize(excel_path)
        logger.info(
            "Excel 报告下载: task_id=%s path=%s size=%s bytes",
            task_id,
            excel_path,
            file_size,
        )
        return send_file(
            excel_path,
            mimetype="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            as_attachment=True,
            download_name=os.path.basename(excel_path),
        )
    except PermissionError as exc:
        logger.exception("Excel 报告读取权限不足: %s", exc)
        return jsonify({"success": False, "error": {"message": "报告读取失败（权限不足）。"}}), 500
    except FileNotFoundError:
        return jsonify({"success": False, "error": {"message": "报告文件不存在。"}}), 404
    except Exception as exc:  # noqa: BLE001
        logger.exception("Excel 报告下载异常: %s", exc)
        return jsonify({"success": False, "error": {"message": "报告下载失败，请稍后重试。"}}), 500


@report_bp.route("/pdf/<string:task_id>", methods=["GET"])
def download_pdf(task_id: str) -> Any:
    """
    PDF 报告下载接口。

    若报告不存在，则先生成后再返回给前端。
    """
    _, pdf_path = _build_report_paths(task_id)
    if not pdf_path:
        return jsonify({"success": False, "error": {"message": "任务不存在。"}}), 404

    if not os.path.exists(pdf_path):
        ok, msg = generate_pdf_report(task_id, pdf_path)
        if not ok:
            return jsonify({"success": False, "error": {"message": msg}}), 500

    try:
        file_size = os.path.getsize(pdf_path)
        logger.info(
            "PDF 报告下载: task_id=%s path=%s size=%s bytes",
            task_id,
            pdf_path,
            file_size,
        )
        return send_file(
            pdf_path,
            mimetype="application/pdf",
            as_attachment=True,
            download_name=os.path.basename(pdf_path),
        )
    except PermissionError as exc:
        logger.exception("PDF 报告读取权限不足: %s", exc)
        return jsonify({"success": False, "error": {"message": "报告读取失败（权限不足）。"}}), 500
    except FileNotFoundError:
        return jsonify({"success": False, "error": {"message": "报告文件不存在。"}}), 404
    except Exception as exc:  # noqa: BLE001
        logger.exception("PDF 报告下载异常: %s", exc)
        return jsonify({"success": False, "error": {"message": "报告下载失败，请稍后重试。"}}), 500


__all__ = ["report_bp"]

