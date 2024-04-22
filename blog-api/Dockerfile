FROM php:8.2-cli

WORKDIR /usr/src/app

COPY composer.lock composer.json /usr/src/app/

RUN apt-get update && \
    apt-get install -y \
        git \
        unzip \
        libzip-dev \
    && docker-php-ext-install zip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-scripts --no-autoloader


COPY . /usr/src/app/
# COPY entrypoint.sh /usr/src/app/entrypoint.sh

RUN docker-php-ext-install pdo pdo_mysql
RUN composer dump-autoload --optimize --no-scripts

EXPOSE 8001

# CMD ["php", "-S", "0.0.0.0:8001", "-t", "public"]

# ENTRYPOINT [ "/usr/src/app/entrypoint.sh" ]


