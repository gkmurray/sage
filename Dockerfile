FROM wordpress:latest

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN useradd -rm -d /home/php -s /bin/bash -g www-data -G sudo -u 1000 php