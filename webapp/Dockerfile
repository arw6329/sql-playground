FROM node

WORKDIR /build
COPY ./src/frontend /build

RUN npm run build

FROM trafex/php-nginx

USER root

RUN apk add php83-pdo php83-pdo_pgsql php83-pdo_mysql

WORKDIR /var/www/php-private
RUN wget https://raw.githubusercontent.com/composer/getcomposer.org/76a7060ccb93902cd7576b67264ad91c8a2700e2/web/installer -O - -q | php -- --quiet
COPY ./composer.json /var/www/php-private
RUN php composer.phar dump-autoload

COPY ./php.ini /etc/php83/conf.d/settings.ini

COPY ./src/SQLPG /var/www/php-private/SQLPG

RUN rm -rf /var/www/html/*
COPY ./src/public /var/www/html/
COPY --from=0 /build/dist/* /var/www/html/

RUN chmod -R 555 /var/www/html /var/www/php-private

USER nobody
