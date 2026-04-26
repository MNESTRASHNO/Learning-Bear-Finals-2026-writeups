from contextlib import asynccontextmanager

from fastapi import FastAPI, Request, Query
from fastapi.responses import HTMLResponse, JSONResponse
from fastapi.templating import Jinja2Templates

from app.config import FLAG
from app.cache import get_client, get_cached_preview, set_cached_preview
from app.fetcher import fetch_url
from app.parser import parse_preview


def seed_redis():
    r = get_client()
    r.hset("apikeys", mapping={
        "service_preview": "pk_preview_a1b2c3d4e5",
        "service_analytics": "pk_analytics_f6g7h8i9j0",
        "master_admin": FLAG,
    })
    r.set("app:version", "1.4.2")
    r.set("app:name", "LinkLens")


@asynccontextmanager
async def lifespan(app: FastAPI):
    seed_redis()
    yield


app = FastAPI(title="LinkLens", docs_url=None, redoc_url=None, lifespan=lifespan)
templates = Jinja2Templates(directory="app/templates")


@app.get("/", response_class=HTMLResponse)
async def index(request: Request):
    return templates.TemplateResponse("index.html", {"request": request})


@app.get("/api/preview")
async def preview(url: str = Query(..., min_length=1)):
    cached = get_cached_preview(url)
    if cached:
        return JSONResponse(content={"preview": cached, "cached": True})

    try:
        raw = fetch_url(url)
    except ValueError as e:
        return JSONResponse(content={"error": str(e)}, status_code=400)
    except Exception:
        return JSONResponse(
            content={"error": "Failed to fetch the URL. Make sure it is reachable."},
            status_code=422,
        )

    preview_data = parse_preview(raw, url)
    set_cached_preview(url, preview_data)
    return JSONResponse(content={"preview": preview_data, "cached": False})


@app.get("/api/health")
async def health():
    r = get_client()
    redis_ok = r.ping()
    version = r.get("app:version")
    return {
        "status": "ok",
        "redis": redis_ok,
        "version": version.decode() if version else None,
    }
