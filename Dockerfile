FROM larueli/php-base-image:7.1

ENV APACHE_ROOT_DIRECTORY="/var/www/html/public"

COPY . /var/www/html/
RUN echo "/usr/local/bin/php artisan migrate --force" > /docker-entrypoint-init.d/migrations.sh && \
    composer install --no-interaction --no-dev --no-ansi && composer dump-autoload --no-dev --classmap-authoritative
