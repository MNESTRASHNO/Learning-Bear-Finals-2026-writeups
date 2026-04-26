FROM php:8.2-apache

RUN apt-get update && \
    apt-get install -y gcc libc6-dev && \
    rm -rf /var/lib/apt/lists/*

COPY getflag.c /tmp/getflag.c
RUN gcc -o /getFlag /tmp/getflag.c && \
    chown root:root /getFlag && \
    chmod 4755 /getFlag && \
    rm /tmp/getflag.c

RUN printf 'flag{redacted}\n' > /flag.txt && \
    chown root:root /flag.txt && \
    chmod 400 /flag.txt

COPY src/ /var/www/html/

RUN mkdir -p /var/www/html/uploads && \
    chown www-data:www-data /var/www/html/uploads && \
    chmod 755 /var/www/html/uploads

EXPOSE 80
CMD ["apache2-foreground"]
