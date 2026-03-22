import logging
import shlex
import subprocess
import time
from typing import List, Tuple
from urllib.parse import urlparse

import requests

from config.settings import GAU_PATH, REQUEST_DELAY, STRICT_COMPLIANCE_MODE
from .compliance import check_authorized_domain

logger = logging.getLogger("scan")


class GauCollector:
    """
    使用 gau 工具收集目标历史 URL 的封装类。

    支持公网域名/IP+端口输入，自动适配 Windows 下 gau.exe 调用，
    并对结果进行去重与基础过滤。
    """

    def __init__(
        self,
        target: str,
        gau_path: str | None = None,
        rate_limit: int = 10,
        timeout: int = 300,
        max_retry: int = 2,
    ) -> None:
        """
        初始化 GauCollector。

        :param target: 目标域名或 IP，可携带端口（例如 "buuctf.cn:8080"）
        :param gau_path: gau 可执行文件路径，默认使用全局配置 GAU_PATH
        :param rate_limit: gau 请求速率限制参数，对应 --rate-limit
        :param timeout: 命令执行超时时间（秒）
        :param max_retry: 失败时的最大重试次数（不含首次执行）
        """
        self.target = target.strip()
        self.gau_path = gau_path or GAU_PATH
        self.rate_limit = rate_limit
        self.timeout = timeout
        self.max_retry = max_retry

    def _build_command(self) -> list[str]:
        """
        构建 gau 命令参数列表。

        自动解析端口并添加 --ports 参数，启用子域收集与 http/https 协议。

        :return: 可直接传给 subprocess.Popen 的参数列表
        """
        # gau 当前版本不支持 --proto / --rate-limit / --ports 等扩展参数，
        # 这里仅对 “host:port” 启用 --subs。
        #
        # 注意：target 可能是完整 URL（如 http://127.0.0.1:7777/index.php），
        # 这时应取 netloc（127.0.0.1:7777）而不是把 path 带进去。
        host = self.target.strip()
        if "://" in host:
            parsed = urlparse(host)
            host = parsed.netloc or host.split("://")[-1].split("/", 1)[0]
        else:
            host = host.split("/", 1)[0]
        host = host.rstrip("/")

        cmd: list[str] = [self.gau_path, host, "--subs"]

        logger.info("构建 gau 命令: %s", " ".join(shlex.quote(arg) for arg in cmd))
        return cmd

    def _run_gau(self) -> Tuple[bool, List[str], str]:
        """
        执行 gau 命令并返回原始 URL 列表。

        内置重试机制（最多 max_retry 次），适配 Windows 下的路径与权限问题。

        :return: (是否成功, 原始 URL 列表, 说明信息)
        """
        # 合规校验（严格模式下不通过则直接拒绝）
        authorized, msg = check_authorized_domain(self.target)
        if not authorized and STRICT_COMPLIANCE_MODE:
            logger.warning("合规校验未通过，终止 URL 收集: target=%s msg=%s", self.target, msg)
            return False, [], f"合规校验未通过：{msg}"

        cmd = self._build_command()
        attempts = 0
        last_error_msg = ""

        while attempts <= self.max_retry:
            attempts += 1
            try:
                logger.info("开始执行 gau（第 %s 次尝试）: target=%s", attempts, self.target)
                process = subprocess.Popen(
                    cmd,
                    stdout=subprocess.PIPE,
                    stderr=subprocess.PIPE,
                    text=True,
                    encoding="utf-8",
                )
                stdout, stderr = process.communicate(timeout=self.timeout)

                if process.returncode != 0:
                    last_error_msg = stderr.strip() or f"gau 执行失败，退出码 {process.returncode}"
                    logger.warning(
                        "gau 执行返回非零退出码: code=%s stderr=%s",
                        process.returncode,
                        last_error_msg,
                    )
                    # 网络波动或远端问题，尝试重试
                    if attempts <= self.max_retry:
                        time.sleep(REQUEST_DELAY)
                        continue
                    # 多次重试仍失败：启用降级方案，避免整个任务失败
                    host, base_path = self._target_host_and_base_path()
                    base_http = f"http://{host}{base_path}"
                    base_https = f"https://{host}{base_path}"
                    raw_urls = [base_http, base_https]
                    logger.info(
                        "gau 多次执行失败，启用基础 URL 降级列表: %s",
                        raw_urls,
                    )
                    return True, raw_urls, f"gau 执行失败：{last_error_msg}，已降级为基础 URL 列表"

                raw_urls = [line.strip() for line in stdout.splitlines() if line.strip()]
                logger.info("gau 执行成功，收集到原始 URL 数量: %s", len(raw_urls))
                return True, raw_urls, "gau 执行成功"
            except FileNotFoundError:
                # 工具未找到：记录警告并采用降级方案，避免整个任务失败
                last_error_msg = f"未找到 gau 工具，将使用基础 URL 列表作为降级方案。请检查 GAU_PATH 配置: {self.gau_path}"
                logger.warning(last_error_msg)

                # 基础降级：仅使用目标的 http/https 根路径，保证后续扫描仍可执行
                host, base_path = self._target_host_and_base_path()
                base_http = f"http://{host}{base_path}"
                base_https = f"https://{host}{base_path}"
                raw_urls = [base_http, base_https]
                logger.info("降级模式启用，使用基础 URL 列表: %s", raw_urls)
                return True, raw_urls, last_error_msg
            except subprocess.TimeoutExpired:
                last_error_msg = f"gau 命令执行超时（>{self.timeout} 秒）"
                logger.warning(last_error_msg)
                process.kill()
                if attempts <= self.max_retry:
                    time.sleep(REQUEST_DELAY)
                    continue
                return False, [], last_error_msg
            except PermissionError as exc:
                last_error_msg = f"gau 执行权限不足: {exc}"
                logger.exception(last_error_msg)
                return False, [], last_error_msg
            except OSError as exc:
                # 包含网络错误、管道错误等系统级异常
                last_error_msg = f"gau 执行系统错误: {exc}"
                logger.exception(last_error_msg)
                if attempts <= self.max_retry:
                    time.sleep(REQUEST_DELAY)
                    continue
                return False, [], last_error_msg
            except Exception as exc:  # noqa: BLE001
                last_error_msg = f"gau 执行未知错误: {exc}"
                logger.exception(last_error_msg)
                if attempts <= self.max_retry:
                    time.sleep(REQUEST_DELAY)
                    continue
                return False, [], last_error_msg

        return False, [], f"gau 执行失败：{last_error_msg}"

    def filter_urls(
        self,
        urls: List[str],
        verify_http: bool = False,
        max_verify: int = 100,
    ) -> List[str]:
        """
        对 URL 列表进行去重与基础过滤。

        过滤规则：
        - 去除空行与重复 URL；
        - 仅保留 http/https 协议；
        - 仅保留域名包含目标主域的 URL；
        - 可选：通过 HTTP 请求过滤明显无效（如 404）的 URL。

        :param urls: 原始 URL 列表
        :param verify_http: 是否对部分 URL 发起 HTTP 请求进行有效性校验
        :param max_verify: 最多进行有效性校验的 URL 数量，避免过多请求
        :return: 过滤后的 URL 列表
        """
        # 目标主机名归一化：支持 target 为 host:port 或 http(s)://host:port/path
        try:
            if "://" in self.target:
                target_norm = (urlparse(self.target).hostname or "").lower()
            else:
                target_norm = self.target.split(":", 1)[0].lower()
        except Exception:  # noqa: BLE001
            target_norm = self.target.split("://")[-1].split(":", 1)[0].lower()
        seen: set[str] = set()
        filtered: list[str] = []

        for url in urls:
            url = url.strip()
            if not url or url in seen:
                continue

            parsed = urlparse(url)
            if parsed.scheme not in ("http", "https"):
                continue

            hostname = (parsed.hostname or "").lower()
            if target_norm not in hostname:
                # 非靶场域名，丢弃
                continue

            seen.add(url)
            filtered.append(url)

        logger.info("URL 过滤完成：原始=%s 去重+过滤后=%s", len(urls), len(filtered))

        if not verify_http or not filtered:
            return filtered

        valid_urls: list[str] = []
        checked = 0

        for url in filtered:
            if checked >= max_verify:
                # 超出最大校验数量，剩余 URL 直接保留
                valid_urls.extend(filtered[checked:])
                break

            try:
                time.sleep(REQUEST_DELAY)
                resp = requests.head(url, timeout=5, allow_redirects=True)
                if resp.status_code < 400:
                    valid_urls.append(url)
                else:
                    logger.debug("URL 校验失败（状态码 %s），丢弃: %s", resp.status_code, url)
            except requests.RequestException as exc:
                logger.debug("URL 校验异常，将视为无效并丢弃: %s 错误=%s", url, exc)
            finally:
                checked += 1

        logger.info(
            "HTTP 有效性校验完成：待校验=%s 有效=%s",
            checked,
            len(valid_urls),
        )
        return valid_urls

    def collect_and_filter(self, verify_http: bool = False, max_verify: int = 100) -> Tuple[bool, List[str], str]:
        """
        综合执行 gau 收集与 URL 过滤。

        :param verify_http: 是否启用 HTTP 有效性校验
        :param max_verify: 最多进行有效性校验的 URL 数量
        :return: (是否成功, 过滤后的 URL 列表, 说明信息)
        """
        ok, raw_urls, msg = self._run_gau()
        if not ok or not raw_urls:
            # gau 完全失败或返回 0 条，启用基础 URL 降级方案，保证后续扫描仍可执行
            host, base_path = self._target_host_and_base_path()
            base_http = f"http://{host}{base_path}"
            base_https = f"https://{host}{base_path}"
            raw_urls = [base_http, base_https]
            logger.warning(
                "gau 未返回有效 URL（ok=%s, count=%s），使用基础 URL 降级列表: %s",
                ok,
                0 if not ok else len(raw_urls),
                raw_urls,
            )
            msg = f"{msg}；已降级为基础 URL 列表"

        filtered = self.filter_urls(raw_urls, verify_http=verify_http, max_verify=max_verify)
        if not filtered:
            # 过滤后为空：对于带路径的本地靶场（如 /dvwa/）很常见，退回基础 URL，保证后续扫描不失败
            host, base_path = self._target_host_and_base_path()
            base_http = f"http://{host}{base_path}"
            base_https = f"https://{host}{base_path}"
            filtered = [base_http, base_https]
            logger.warning("URL 过滤后为空，已回退到基础 URL 列表: %s", filtered)
            msg = f"{msg}；过滤后为空，已回退基础 URL 列表"

        return True, filtered, f"{msg}，过滤后 URL 数量: {len(filtered)}"

    def _target_host_and_base_path(self) -> tuple[str, str]:
        """
        从 target 解析 host:port 与 base_path（可为空或类似 /dvwa）。
        """
        t = (self.target or "").strip()
        if "://" in t:
            p = urlparse(t)
            host = p.netloc or t.split("://")[-1].split("/", 1)[0]
            path = (p.path or "").rstrip("/")
            return host, path
        host = t.split("/", 1)[0].rstrip("/")
        return host, ""


__all__ = ["GauCollector"]

