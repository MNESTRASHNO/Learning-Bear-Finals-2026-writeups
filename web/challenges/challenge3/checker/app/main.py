import re
import httpx
from html import escape
from urllib.parse import urlparse
from fastapi import FastAPI, Query, Request
from fastapi.responses import HTMLResponse, Response
from starlette.middleware.base import BaseHTTPMiddleware

app = FastAPI()

LOCAL_IP = "172.28.0.10"


class HeaderMiddleware(BaseHTTPMiddleware):
    async def dispatch(self, request: Request, call_next):
        response: Response = await call_next(request)
        response.headers["X-Local-IP"] = LOCAL_IP
        return response


app.add_middleware(HeaderMiddleware)

BLOCKED_PATTERNS = [
    re.compile(r"^127\.\d{1,3}\.\d{1,3}\.\d{1,3}$"),
    re.compile(r"^10\.\d{1,3}\.\d{1,3}\.\d{1,3}$"),
    re.compile(r"^172\.(1[6-9]|2\d|3[01])\.\d{1,3}\.\d{1,3}$"),
    re.compile(r"^192\.168\.\d{1,3}\.\d{1,3}$"),
    re.compile(r"^0\.0\.0\.0$"),
]

BLOCKED_HOSTS = {"localhost", "internal"}

TEMPLATE = """<!DOCTYPE html>
<html>
<head>
    <title>Corporate URL Availability Checker</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f6f8;
            color: #1a1a2e;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 48px;
            width: 640px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        h1 {
            text-align: center;
            margin-bottom: 8px;
            font-size: 1.5em;
            font-weight: 600;
            color: #111827;
        }
        .subtitle {
            text-align: center;
            color: #6b7280;
            font-size: 0.9em;
            margin-bottom: 32px;
        }
        .form-group {
            display: flex;
            gap: 10px;
            margin-bottom: 24px;
        }
        input[type="text"] {
            flex: 1;
            padding: 10px 14px;
            background: #f9fafb;
            border: 1px solid #d1d5db;
            color: #111827;
            font-size: 0.95em;
            border-radius: 4px;
            outline: none;
            transition: border-color 0.15s;
        }
        input[type="text"]:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }
        button {
            padding: 10px 28px;
            background: #2563eb;
            color: #ffffff;
            border: none;
            font-weight: 600;
            font-size: 0.95em;
            cursor: pointer;
            border-radius: 4px;
            transition: background 0.15s;
        }
        button:hover { background: #1d4ed8; }
        .result {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 20px;
            white-space: pre-wrap;
            word-break: break-all;
            max-height: 400px;
            overflow-y: auto;
            font-family: 'Consolas', 'Courier New', monospace;
            font-size: 0.85em;
            color: #374151;
            line-height: 1.6;
        }
        .error {
            color: #dc2626;
            border-color: #fca5a5;
            background: #fef2f2;
        }
        .footer {
            text-align: center;
            margin-top: 24px;
            font-size: 0.75em;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>URL Availability Checker</h1>
        <p class="subtitle">Enter a URL to verify its availability and response status.</p>
        <form method="GET" action="/check">
            <div class="form-group">
                <input type="text" name="url" placeholder="https://example.com" value="$URL$">
                <button type="submit">Check</button>
            </div>
        </form>
        $RESULT$
        <div class="footer">Internal Network Tool &mdash; Authorized Use Only</div>
    </div>
</body>
</html>"""


def render(url="", result=""):
    return TEMPLATE.replace("$URL$", escape(url)).replace("$RESULT$", result)


def is_blocked(hostname: str) -> bool:
    hostname = hostname.lower().strip("[]")
    if hostname in BLOCKED_HOSTS:
        return True
    for pattern in BLOCKED_PATTERNS:
        if pattern.match(hostname):
            return True
    return False


@app.get("/", response_class=HTMLResponse)
def index():
    return render()


@app.get("/check", response_class=HTMLResponse)
def check(url: str = Query("")):
    if not url:
        return render(result='<div class="result error">No URL provided.</div>')

    try:
        parsed = urlparse(url)
    except Exception:
        return render(url=url, result='<div class="result error">Invalid URL.</div>')

    hostname = parsed.hostname or ""

    if not parsed.scheme or parsed.scheme not in ("http", "https"):
        return render(url=url, result='<div class="result error">Only http and https schemes are permitted.</div>')

    if is_blocked(hostname):
        return render(url=url, result='<div class="result error">Access to internal addresses is restricted.</div>')

    try:
        with httpx.Client(follow_redirects=False, timeout=5) as client:
            resp = client.get(url)
        body = escape(resp.text[:4096])
        info = f"Status: {resp.status_code}\n\n{body}"
        return render(url=url, result=f'<div class="result">{info}</div>')
    except Exception as e:
        return render(url=url, result=f'<div class="result error">Request failed: {escape(str(e))}</div>')
