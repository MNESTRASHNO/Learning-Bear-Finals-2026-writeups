import json
import hashlib
import redis

from app.config import REDIS_HOST, REDIS_PORT, REDIS_DB, PREVIEW_CACHE_TTL

pool = redis.ConnectionPool(host=REDIS_HOST, port=REDIS_PORT, db=REDIS_DB)


def get_client():
    return redis.Redis(connection_pool=pool)


def cache_key(url: str) -> str:
    h = hashlib.sha256(url.encode()).hexdigest()[:16]
    return f"preview:{h}"


def get_cached_preview(url: str) -> dict | None:
    r = get_client()
    data = r.get(cache_key(url))
    if data:
        return json.loads(data)
    return None


def set_cached_preview(url: str, preview: dict):
    r = get_client()
    r.setex(cache_key(url), PREVIEW_CACHE_TTL, json.dumps(preview))
