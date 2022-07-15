FROM composer:2 AS composer

COPY /api /var/www/
WORKDIR /var/www

RUN composer install \
    --optimize-autoloader \
    --ignore-platform-reqs \
    --no-scripts \
    --no-dev \
    && composer dump-autoload

FROM php:8.1-cli-buster

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        libssl-dev \
        libpq-dev \
        libmcrypt-dev \
        libxml2-dev \
        libzip-dev \
        unzip \
        && rm -r /var/lib/apt/lists/*

# Install extention
RUN docker-php-ext-install soap pcntl zip pdo_pgsql bcmath sockets

#####################################
#
#--------------------------------------------------------------------------
# Final Touch
#--------------------------------------------------------------------------
#

####################################
# OPCache:
####################################
RUN docker-php-ext-install opcache

COPY .docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

ADD .docker/php/local.ini /usr/local/etc/php/conf.d

RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

COPY --from=composer /var/www /var/www
WORKDIR /var/www

RUN ./vendor/bin/rr get-binary

########################
# Optimize
#
########################
RUN php artisan route:cache && php artisan config:cache

VOLUME ["/var/www"]
CMD /bin/bash -c 'php artisan octane:start --server=roadrunner --host=0.0.0.0 --port=8080 --rr-config=.rr.yaml --workers=2'
