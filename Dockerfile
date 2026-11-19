FROM php:8.3-apache

RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www/html

COPY app/ /var/www/html/

EXPOSE 80