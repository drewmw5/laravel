FROM php:8.1.4RC1-fpm-alpine

RUN docker-php-ext-install pdo pdo_mysql
