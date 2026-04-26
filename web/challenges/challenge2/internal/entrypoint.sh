#!/bin/sh
hostname -i > /shared/internal-api-ip
exec python app.py
