FROM php:8.2-apache

RUN apt-get update && \
    apt-get install -y gcc libc6-dev && \
    rm -rf /var/lib/apt/lists/*

COPY getflag.c /tmp/getflag.c
RUN gcc -o /getFlag /tmp/getflag.c && \
    chown root:root /getFlag && \
    chmod 4755 /getFlag && \
    rm /tmp/getflag.c

COPY flag.txt /flag.txt
RUN chown root:root /flag.txt && \
    chmod 400 /flag.txt

RUN echo '\
session.save_path = /tmp\n\
session.upload_progress.enabled = On\n\
session.upload_progress.cleanup = On\n\
session.upload_progress.prefix = upload_progress_\n\
session.upload_progress.name = PHP_SESSION_UPLOAD_PROGRESS\n\
session.upload_progress.freq = 1%\n\
session.upload_progress.min_freq = 0\n\
' > /usr/local/etc/php/conf.d/session.ini

COPY index.php /var/www/html/index.php

EXPOSE 80
