FROM python:3.11-slim

RUN apt-get update && \
    apt-get install -y --no-install-recommends libcurl4-openssl-dev gcc && \
    rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

COPY app/ ./app/

ENV FLAG=flag{redacted}
ENV REDIS_HOST=redis

EXPOSE 11005

CMD ["uvicorn", "app.main:app", "--host", "0.0.0.0", "--port", "11005"]
