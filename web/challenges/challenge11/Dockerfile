FROM php:7.4-apache

COPY src/ /var/www/html/

RUN mkdir -p /var/www/html/uploads \
    && chown -R www-data:www-data /var/www/html/uploads \
    && chmod 755 /var/www/html/uploads \
    && printf "upload_max_filesize = 10M\npost_max_size = 10M\n" > /usr/local/etc/php/conf.d/uploads.ini

COPY flag.txt /flag.txt
RUN chmod 444 /flag.txt

EXPOSE 80
