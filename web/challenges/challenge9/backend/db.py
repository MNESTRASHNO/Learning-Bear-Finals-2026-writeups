import os
import redis

REDIS_HOST = os.environ.get("REDIS_HOST", "127.0.0.1")

db = redis.Redis(host=REDIS_HOST, port=6379, db=0, decode_responses=True)
