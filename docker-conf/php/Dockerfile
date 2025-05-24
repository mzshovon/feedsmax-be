FROM php:8.2-fpm-bullseye

RUN apt-get update && apt-get install -y libpq-dev libldap2-dev locales libzip-dev zip \
    && pecl install redis && docker-php-ext-enable redis \
    && docker-php-ext-install gettext pdo pdo_mysql pdo_pgsql mysqli bcmath zip

RUN curl -sS https://getcomposer.org/installer | \
php -- --install-dir=/usr/bin/ --filename=composer
