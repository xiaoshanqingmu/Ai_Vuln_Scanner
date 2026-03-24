import logging
from typing import List, Optional, Tuple

from flask import Flask

from app import db
from config.settings import DATABASE_URI
from .models import ScanTask, Vulnerability

logger = logging.getLogger("app.db")


def init_db(app: Flask) -> None:
    """
    初始化数据库配置与表结构。

    该函数主要用于在应用启动阶段或独立脚本中初始化数据库。

    :param app: Flask 应用实例
    """
    try:
        # 确保数据库配置正确
        app.config.setdefault("SQLALCHEMY_DATABASE_URI", DATABASE_URI)
        app.config.setdefault("SQLALCHEMY_TRACK_MODIFICATIONS", False)

        # 为长时间任务存储预留的引擎配置（如连接预检查）
        engine_options = app.config.get("SQLALCHEMY_ENGINE_OPTIONS", {})
        engine_options.setdefault("pool_pre_ping", True)
        engine_options.setdefault("pool_recycle", 1800)
        app.config["SQLALCHEMY_ENGINE_OPTIONS"] = engine_options

        db.init_app(app)

        with app.app_context():
            db.create_all()
        logger.info("数据库初始化成功")
    except Exception as exc:  # noqa: BLE001
        logger.exception("数据库初始化失败: %s", exc)
        raise


def add_scan_task(task_id: str, domain: str, status: str = "pending") -> Tuple[bool, Optional[ScanTask], str]:
    """
    新增扫描任务记录。

    :param task_id: 任务 ID（可为 Celery 任务 ID 或内部生成 ID）
    :param domain: 扫描目标域名或 IP，支持携带端口，如 buuctf.cn:8080
    :param status: 初始任务状态，默认值为 pending
    :return: (是否成功, 新建的 ScanTask 实例或 None, 友好提示信息)
    """
    try:
        scan_task = ScanTask(task_id=task_id, domain=domain, status=status)
        db.session.add(scan_task)
        db.session.commit()
        logger.info("新增扫描任务成功: task_id=%s domain=%s status=%s", task_id, domain, status)
        return True, scan_task, "新增扫描任务成功"
    except Exception as exc:  # noqa: BLE001
        db.session.rollback()
        logger.exception("新增扫描任务失败: task_id=%s domain=%s 错误: %s", task_id, domain, exc)
        return False, None, "新增扫描任务失败，请稍后重试"


def get_scan_task_by_id(scan_task_id: int) -> Optional[ScanTask]:
    """
    根据自增 ID 获取扫描任务记录。

    :param scan_task_id: ScanTask.id 主键 ID
    :return: 对应的 ScanTask 实例或 None
    """
    try:
        scan_task = ScanTask.query.get(scan_task_id)
        if scan_task:
            logger.info("查询扫描任务成功: id=%s", scan_task_id)
        else:
            logger.warning("查询扫描任务未找到: id=%s", scan_task_id)
        return scan_task
    except Exception as exc:  # noqa: BLE001
        logger.exception("查询扫描任务失败: id=%s 错误: %s", scan_task_id, exc)
        return None


def update_scan_task_status(scan_task_id: int, status: str) -> Tuple[bool, Optional[ScanTask], str]:
    """
    更新扫描任务状态。

    :param scan_task_id: ScanTask.id 主键 ID
    :param status: 新的任务状态，如 running/finished/failed
    :return: (是否成功, 更新后的 ScanTask 实例或 None, 友好提示信息)
    """
    try:
        scan_task = ScanTask.query.get(scan_task_id)
        if not scan_task:
            logger.warning("更新扫描任务状态失败，任务不存在: id=%s", scan_task_id)
            return False, None, "扫描任务不存在"

        scan_task.status = status
        if status in ("finished", "failed") and not scan_task.finish_time:
            # 仅在任务结束时更新完成时间
            from datetime import datetime as _dt

            scan_task.finish_time = _dt.utcnow()

        db.session.commit()
        logger.info("更新扫描任务状态成功: id=%s status=%s", scan_task_id, status)
        return True, scan_task, "更新任务状态成功"
    except Exception as exc:  # noqa: BLE001
        db.session.rollback()
        logger.exception("更新扫描任务状态失败: id=%s 错误: %s", scan_task_id, exc)
        return False, None, "更新任务状态失败，请稍后重试"


def get_all_scan_tasks(limit: int = 100) -> List[ScanTask]:
    """
    获取最近的扫描任务列表。

    :param limit: 返回记录的最大条数，默认 100 条
    :return: ScanTask 实例列表
    """
    try:
        tasks = (
            ScanTask.query.order_by(ScanTask.create_time.desc())
            .limit(limit)
            .all()
        )
        logger.info("获取扫描任务列表成功，数量: %s", len(tasks))
        return tasks
    except Exception as exc:  # noqa: BLE001
        logger.exception("获取扫描任务列表失败: %s", exc)
        return []


def add_vulnerability(
    task_id: str,
    tool: str,
    vuln_name: str,
    risk_level: str,
    url: str,
    param: Optional[str] = None,
    description: Optional[str] = None,
) -> Tuple[bool, Optional[Vulnerability], str]:
    """
    新增漏洞记录。

    :param task_id: 关联的扫描任务 ID
    :param tool: 漏洞来源工具名称，如 ZAP、nuclei 等
    :param vuln_name: 漏洞名称
    :param risk_level: 风险等级，建议统一为小写，如 high/medium/low/info/critical
    :param url: 漏洞对应的 URL，支持带端口的公网地址
    :param param: 受影响的参数名或位置
    :param description: 工具原始描述信息
    :return: (是否成功, 新建的 Vulnerability 实例或 None, 友好提示信息)
    """
    try:
        normalized_risk = risk_level.lower()
        # 去重：同一任务下同一工具+漏洞名+URL+参数只保留一条，减少 AI 重复分析与报告噪声。
        existed = (
            Vulnerability.query.filter_by(
                task_id=task_id,
                tool=tool,
                vuln_name=vuln_name,
                url=url,
                param=param,
            )
            .order_by(Vulnerability.id.desc())
            .first()
        )
        if existed:
            logger.info(
                "漏洞记录去重命中，跳过重复写入: task_id=%s tool=%s vuln_name=%s url=%s",
                task_id,
                tool,
                vuln_name,
                url,
            )
            return True, existed, "重复漏洞已跳过"

        vulnerability = Vulnerability(
            task_id=task_id,
            tool=tool,
            vuln_name=vuln_name,
            risk_level=normalized_risk,
            url=url,
            param=param,
            description=description,
        )
        db.session.add(vulnerability)
        db.session.commit()
        logger.info(
            "新增漏洞记录成功: task_id=%s tool=%s vuln_name=%s risk_level=%s",
            task_id,
            tool,
            vuln_name,
            normalized_risk,
        )
        return True, vulnerability, "新增漏洞记录成功"
    except Exception as exc:  # noqa: BLE001
        db.session.rollback()
        logger.exception("新增漏洞记录失败: task_id=%s 错误: %s", task_id, exc)
        return False, None, "新增漏洞记录失败，请稍后重试"


def get_vulnerabilities_by_task_id(task_id: str) -> List[Vulnerability]:
    """
    根据任务 ID 获取所有漏洞记录。

    :param task_id: 关联的扫描任务 ID
    :return: Vulnerability 实例列表
    """
    try:
        vulns = (
            Vulnerability.query.filter_by(task_id=task_id)
            .order_by(Vulnerability.create_time.desc())
            .all()
        )
        logger.info("根据任务 ID 获取漏洞记录成功: task_id=%s 数量=%s", task_id, len(vulns))
        return vulns
    except Exception as exc:  # noqa: BLE001
        logger.exception("根据任务 ID 获取漏洞记录失败: task_id=%s 错误: %s", task_id, exc)
        return []


def update_vulnerability_ai_info(
    vulnerability_id: int,
    attack_principle: Optional[str] = None,
    poc_suggestion: Optional[str] = None,
    custom_fix: Optional[str] = None,
    attack_chain_risk: Optional[str] = None,
) -> Tuple[bool, Optional[Vulnerability], str]:
    """
    更新漏洞记录的 AI 分析信息。

    :param vulnerability_id: 漏洞记录主键 ID
    :param attack_principle: 攻击原理分析内容
    :param poc_suggestion: PoC 构造建议
    :param custom_fix: 自定义修复建议
    :param attack_chain_risk: 在攻击链中的风险角色评估
    :return: (是否成功, 更新后的 Vulnerability 实例或 None, 友好提示信息)
    """
    try:
        vulnerability = Vulnerability.query.get(vulnerability_id)
        if not vulnerability:
            logger.warning("更新 AI 分析信息失败，漏洞记录不存在: id=%s", vulnerability_id)
            return False, None, "漏洞记录不存在"

        if attack_principle is not None:
            vulnerability.attack_principle = attack_principle
        if poc_suggestion is not None:
            vulnerability.poc_suggestion = poc_suggestion
        if custom_fix is not None:
            vulnerability.custom_fix = custom_fix
        if attack_chain_risk is not None:
            vulnerability.attack_chain_risk = attack_chain_risk

        db.session.commit()
        logger.info("更新漏洞 AI 分析信息成功: id=%s", vulnerability_id)
        return True, vulnerability, "更新漏洞 AI 分析信息成功"
    except Exception as exc:  # noqa: BLE001
        db.session.rollback()
        logger.exception("更新漏洞 AI 分析信息失败: id=%s 错误: %s", vulnerability_id, exc)
        return False, None, "更新漏洞 AI 分析信息失败，请稍后重试"


