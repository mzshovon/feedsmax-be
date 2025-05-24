FROM php:8.1-fpm-bullseye

ENV TZ="Asia/Dhaka"

RUN apt-get update && apt-get install -y libpq-dev libldap2-dev locales libzip-dev zip cron libpng-dev \
    && pecl install redis && docker-php-ext-enable redis \
    && docker-php-ext-install gettext pdo pdo_mysql pdo_pgsql mysqli bcmath zip gd

RUN curl -sS https://getcomposer.org/installer | \
php -- --install-dir=/usr/bin/ --filename=composer

RUN apt-get update && apt-get install -y supervisor

COPY worker.conf /etc/supervisor/conf.d/worker.conf

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/worker.conf"]
