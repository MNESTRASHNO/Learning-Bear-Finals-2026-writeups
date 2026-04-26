#!/bin/sh
echo "nameserver 8.8.8.8" > /etc/resolv.conf
python /challenge/resolver.py &
sleep 1
echo "nameserver 127.0.0.1" > /etc/resolv.conf
exec uvicorn app.main:app --host 0.0.0.0 --port 8000
