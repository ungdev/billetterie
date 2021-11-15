FROM larueli/php-base-image:7.1

USER 0

ENV APACHE_DOCUMENT_ROOT="/var/www/html/public"

COPY . /var/www/html/
COPY php.ini /usr/local/etc/php/conf.d/billetterie.ini

RUN echo "cd /var/www/html && /usr/local/bin/php artisan migrate --force" > /docker-entrypoint-init.d/a_migrations.sh && \
    echo "cd /var/www/html && /usr/local/bin/php artisan db:seed --force" > /docker-entrypoint-init.d/a_migrations_dbseed.sh && \
    echo 'cd /var/www/html && /usr/local/bin/php -d memory_limit=-1 artisan queue:work --sleep=3 --tries=3 --daemon &' > /docker-entrypoint-init.d/b_queue.sh && \
    composer install --no-interaction --no-dev --no-ansi && composer dump-autoload --no-dev --classmap-authoritative && php artisan cache:clear && \
    php artisan optimize && \
    chmod g+rwx -R /var/www/html

USER 675654
