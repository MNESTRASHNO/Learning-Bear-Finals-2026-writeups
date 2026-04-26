FROM php:8.2-apache

RUN a2enmod rewrite

RUN echo "register_argc_argv = On" > /usr/local/etc/php/conf.d/pear.ini

COPY src/ /var/www/html/

RUN echo "flag{redacted}" > /flag.txt && \
    chmod 444 /flag.txt

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
