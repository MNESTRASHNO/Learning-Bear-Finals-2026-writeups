#!/bin/sh
while [ ! -s /shared/internal-api-ip ]; do sleep 0.1; done
IP=$(cat /shared/internal-api-ip)
echo "$IP internal-api" >> /etc/hosts
exec python app.py
