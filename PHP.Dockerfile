ARG PHP_VERSION=7.4-fpm-alpine
ARG COMPOSER_VERSION=2

FROM composer:${COMPOSER_VERSION} AS composer


FROM php:${PHP_VERSION}

RUN set -eux \
    && apk update --no-cache \
    && apk upgrade --no-cache \
    && apk add --no-cache \
        autoconf \
        g++ \
        make \
        postgresql-dev \
        yaml-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
    && pecl channel-update pecl.php.net \
    && pecl install yaml-2.0.4 \
    && echo 'extension=yaml.so' > /usr/local/etc/php/conf.d/ext-yaml.ini \
    && docker-php-ext-enable yaml

COPY . /var/www/blog
WORKDIR /var/www/blog

COPY --from=composer /usr/bin/composer /usr/local/bin/composer
RUN composer install --no-interaction

CMD ["php-fpm"]