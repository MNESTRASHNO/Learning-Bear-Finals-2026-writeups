import os
from fastapi import FastAPI

app = FastAPI()

FLAG = os.environ.get("FLAG", "flag{redacted}")


@app.get("/")
def root():
    return {"status": "ok"}


@app.get("/flag")
def flag():
    return {"flag": FLAG}
