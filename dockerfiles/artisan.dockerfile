FROM php:8.1.4RC1-fpm-alpine

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN docker-php-ext-install pdo pdo_mysql
