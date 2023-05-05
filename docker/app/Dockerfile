FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    postgresql-client

COPY ./docker/app/php.ini /usr/local/etc/php/conf.d/php.ini


# Install composer
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- \
    --filename=composer \
    --install-dir=/usr/local/bin

RUN docker-php-ext-install pdo_mysql

WORKDIR /var/www
