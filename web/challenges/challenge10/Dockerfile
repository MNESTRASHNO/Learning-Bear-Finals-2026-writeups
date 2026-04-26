FROM php:8.3-apache

COPY flag.txt /flag.txt
RUN chmod 444 /flag.txt

COPY src/ /var/www/html/

EXPOSE 80
