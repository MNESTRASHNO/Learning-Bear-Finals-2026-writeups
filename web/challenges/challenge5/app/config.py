import os

REDIS_HOST = os.environ.get("REDIS_HOST", "redis")
REDIS_PORT = 6379
REDIS_DB = 0

FLAG = os.environ.get("FLAG", "flag{redacted}")

PREVIEW_CACHE_TTL = 600

FETCH_TIMEOUT = 5
MAX_RESPONSE_SIZE = 1024 * 512
