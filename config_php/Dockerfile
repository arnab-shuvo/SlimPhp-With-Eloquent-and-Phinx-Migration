FROM php:7.3.4-fpm-alpine3.8
#FROM php:7.0-fpm

RUN docker-php-ext-install pdo_mysql mysqli

RUN apk update && apk add \
    bash \
    nano \
  && rm -rf /var/cache/apk/*


RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/bin/ --filename=composer

COPY ./entrypoint.sh /var/www/html/entrypoint.sh

RUN chmod +x /var/www/html/entrypoint.sh

ENTRYPOINT cd app && vendor/bin/phinx migrate
CMD ["true"]



