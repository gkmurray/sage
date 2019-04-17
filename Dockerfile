FROM wordpress:php7.1

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY docker/bin/entrypoint.sh /custom-entrypoint.sh

ENTRYPOINT [ "/custom-entrypoint.sh" ]