#!/bin/sh

echo 'My Entry'

chown -R www-data:www-data /var/www/html/wp-content

cd /var/www/html/wp-content/themes/sage
export ACF_PRO_KEY=<ADD-KEY-HERE>
composer install --no-progress --no-suggest --no-interaction

exec apache2-foreground "$@"
