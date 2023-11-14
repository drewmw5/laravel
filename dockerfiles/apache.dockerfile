FROM php:apache

USER root

WORKDIR /var/www/html

RUN docker-php-ext-install pdo_mysql \
    && docker-php-ext-enable pdo_mysql

RUN pecl install -o -f xdebug \
    && docker-php-ext-enable xdebug

COPY apache/php.ini /usr/local/etc/php/

COPY apache/apache2.conf /etc/apache2/apache2.conf
COPY apache/vhost.conf /etc/apache2/sites-available/000-default.conf
COPY apache/vhost-ssl.conf /etc/apache2/sites-available/default-ssl.conf

RUN a2ensite 000-default
RUN a2ensite default-ssl

RUN a2enmod rewrite
RUN a2enmod ssl
RUN a2enmod proxy_http
RUN a2enmod proxy
RUN a2enmod proxy_wstunnel
RUN a2enmod headers
