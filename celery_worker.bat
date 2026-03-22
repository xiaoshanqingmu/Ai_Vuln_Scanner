@echo off
REM ============================================
REM  Celery Worker 启动脚本（Windows）
REM  请在已激活的虚拟环境中运行本脚本：
REM      venv\Scripts\activate
REM      celery_worker.bat
REM ============================================

cd /d %~dp0

REM 使用 app.tasks 中的 Celery 实例（在 tasks/celery_app.py 中定义）
celery -A tasks.celery_app.celery_app worker --loglevel=info

