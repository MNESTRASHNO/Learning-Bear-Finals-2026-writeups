FROM php:8.1-apache

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

# Custom PHP config to widen the race window
COPY php.ini /usr/local/etc/php/php.ini

# Application files
COPY index.php /var/www/html/
COPY phpinfo.php /var/www/html/
COPY pages/ /var/www/html/pages/

# Ensure /tmp is writable for PHP temp uploads
RUN chmod 1777 /tmp

EXPOSE 80
