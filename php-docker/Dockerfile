FROM php:8.1-fpm-alpine

RUN mkdir -p /webapp/storage

RUN chown -R $(whoami):www-data /webapp/storage && chmod -R ug+w /webapp/storage
RUN docker-php-ext-install pdo pdo_mysql
RUN apk add --no-cache zip libzip-dev libpng-dev
RUN docker-php-ext-install zip
RUN docker-php-ext-install gd
