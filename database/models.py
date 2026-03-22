from datetime import datetime

from app import db


class ScanTask(db.Model):
    """
    扫描任务模型。

    用于记录每一次扫描任务的基本信息和执行状态。
    """

    __tablename__ = "scan_tasks"

    id = db.Column(db.Integer, primary_key=True)  # 主键自增 ID
    task_id = db.Column(db.String(64), unique=True, nullable=False, index=True)  # Celery 任务 ID 或内部任务 ID
    domain = db.Column(db.String(255), nullable=False, index=True)  # 目标域名或 IP，支持携带端口信息，如 buuctf.cn:8080
    status = db.Column(db.String(32), nullable=False, default="pending", index=True)  # 任务状态，如 pending/running/finished/failed
    create_time = db.Column(db.DateTime, nullable=False, default=datetime.utcnow)  # 任务创建时间（UTC）
    finish_time = db.Column(db.DateTime, nullable=True)  # 任务完成时间（UTC），未完成时为 None

    def __repr__(self) -> str:
        """
        返回用于调试的字符串表示。
        """
        return f"<ScanTask id={self.id} task_id={self.task_id} domain={self.domain} status={self.status}>"


class Vulnerability(db.Model):
    """
    漏洞信息模型。

    用于记录各扫描工具发现的漏洞信息以及 AI 分析结果。
    """

    __tablename__ = "vulnerabilities"

    id = db.Column(db.Integer, primary_key=True)  # 主键自增 ID
    task_id = db.Column(db.String(64), nullable=False, index=True)  # 关联的扫描任务 ID，对应 ScanTask.task_id
    tool = db.Column(db.String(64), nullable=False)  # 漏洞来源工具，如 ZAP、nuclei 等
    vuln_name = db.Column(db.String(255), nullable=False)  # 漏洞名称
    risk_level = db.Column(db.String(32), nullable=False, index=True)  # 风险等级，兼容 ZAP 和 nuclei，如 high/medium/low/info/critical
    url = db.Column(db.Text, nullable=False)  # 漏洞对应的 URL，支持带端口的公网 URL
    param = db.Column(db.String(255), nullable=True)  # 受影响的参数名或位置
    description = db.Column(db.Text, nullable=True)  # 工具原始描述信息
    create_time = db.Column(db.DateTime, nullable=False, default=datetime.utcnow)  # 漏洞记录时间（UTC）

    # AI 分析字段
    attack_principle = db.Column(db.Text, nullable=True)  # 攻击原理分析
    poc_suggestion = db.Column(db.Text, nullable=True)  # PoC 构造建议
    custom_fix = db.Column(db.Text, nullable=True)  # 自定义修复建议（结合业务语境）
    attack_chain_risk = db.Column(db.Text, nullable=True)  # 在攻击链中的风险角色评估

    def __repr__(self) -> str:
        """
        返回用于调试的字符串表示。
        """
        return f"<Vulnerability id={self.id} task_id={self.task_id} vuln_name={self.vuln_name} risk_level={self.risk_level}>"


