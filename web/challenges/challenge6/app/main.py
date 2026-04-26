import os

from fastapi import FastAPI, Request, Form
from fastapi.responses import HTMLResponse
from fastapi.templating import Jinja2Templates
import httpx

from app.security import validate_url

app = FastAPI(docs_url=None, redoc_url=None)
templates = Jinja2Templates(directory="app/templates")

FLAG = os.environ.get("FLAG", "flag{redacted}")


@app.get("/", response_class=HTMLResponse)
async def index(request: Request):
    return templates.TemplateResponse("index.html", {"request": request})


@app.post("/fetch", response_class=HTMLResponse)
async def fetch_url(request: Request, url: str = Form(...)):
    try:
        validated_url = validate_url(url)
    except ValueError as e:
        return templates.TemplateResponse(
            "result.html",
            {"request": request, "error": str(e), "content": None, "url": url},
        )

    try:
        async with httpx.AsyncClient(follow_redirects=False, timeout=5.0) as client:
            response = await client.get(validated_url)

        if response.is_redirect:
            return templates.TemplateResponse(
                "result.html",
                {
                    "request": request,
                    "error": "Redirects are not allowed",
                    "content": None,
                    "url": url,
                },
            )

        content = response.text

    except httpx.TimeoutException:
        return templates.TemplateResponse(
            "result.html",
            {
                "request": request,
                "error": "Request timed out",
                "content": None,
                "url": url,
            },
        )
    except Exception as e:
        return templates.TemplateResponse(
            "result.html",
            {
                "request": request,
                "error": f"Failed to fetch URL: {type(e).__name__}",
                "content": None,
                "url": url,
            },
        )

    return templates.TemplateResponse(
        "result.html",
        {"request": request, "error": None, "content": content, "url": url},
    )


@app.get("/flag")
async def flag(request: Request):
    client_ip = request.client.host
    if client_ip not in ("127.0.0.1", "::1"):
        return {"error": "Forbidden: this endpoint is only accessible from localhost"}
    return {"flag": FLAG}
