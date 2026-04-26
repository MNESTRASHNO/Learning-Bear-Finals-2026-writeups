FROM php:7.4-apache-bullseye

RUN echo "nameserver 8.8.8.8" > /etc/resolv.conf && \
    apt-get update && apt-get install -y tcl-expect-dev && \
    PHP_EXPECT_EXPECT_INCLUDEDIR=/usr/include/tcl8.6 \
    PHP_EXPECT_EXPECT_LIBDIR=/usr/lib/x86_64-linux-gnu \
    pecl install expect && \
    docker-php-ext-enable expect && \
    rm -rf /var/lib/apt/lists/*

COPY flag.txt /flag.txt
RUN chmod 444 /flag.txt

COPY src/ /var/www/html/

EXPOSE 80
