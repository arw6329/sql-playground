FROM trafex/php-nginx:3.6.0

ENV PHP_VERSION=83

USER root

RUN apk add php${PHP_VERSION}-pdo php${PHP_VERSION}-pdo_pgsql php${PHP_VERSION}-pdo_mysql php${PHP_VERSION}-phar php${PHP_VERSION}-mbstring

# https://github.com/ApOgEE/php-oci8-alpine
ENV LD_LIBRARY_PATH /usr/local/instantclient
ENV ORACLE_HOME /usr/local/instantclient
# Install Oracle Client and build OCI8 (Oracle Command Interface 8 - PHP extension)
RUN apk add php${PHP_VERSION}-pear php${PHP_VERSION}-dev gcc musl-dev gcompat make libnsl libaio
## Download and unarchive Instant Client
RUN curl -o /tmp/basic.zip https://download.oracle.com/otn_software/linux/instantclient/instantclient-basic-linuxx64.zip -SL
RUN curl -o /tmp/sdk.zip https://download.oracle.com/otn_software/linux/instantclient/instantclient-sdk-linuxx64.zip
# RUN curl -o /tmp/sqlplus.zip https://download.oracle.com/otn_software/linux/instantclient/instantclient-sqlplus-linuxx64.zip
RUN unzip -d /tmp/unzip1/ /tmp/basic.zip
RUN unzip -d /tmp/unzip2/ /tmp/sdk.zip
## Links are required for older SDKs
RUN mkdir "$ORACLE_HOME"
RUN cp -R /tmp/unzip1/instantclient*/* "$ORACLE_HOME"
RUN cp -R /tmp/unzip2/instantclient*/* "$ORACLE_HOME"
## Build OCI8 with PECL
RUN echo "instantclient,${ORACLE_HOME}" | pecl install pdo_oci
RUN echo "instantclient,${ORACLE_HOME}" | pecl install oci8
#  Clean up
RUN apk del php${PHP_VERSION}-pear gcc musl-dev make
RUN rm -rf /tmp/*

WORKDIR /var/www/php-private
RUN wget https://raw.githubusercontent.com/composer/getcomposer.org/76a7060ccb93902cd7576b67264ad91c8a2700e2/web/installer -O - -q | php -- --quiet
COPY ./composer.json /var/www/php-private
RUN php composer.phar dump-autoload

COPY ./php.ini /etc/php${PHP_VERSION}/conf.d/settings.ini

COPY ./src/SQLPG /var/www/php-private/SQLPG

RUN rm -rf /var/www/html/*
COPY ./src/public /var/www/html/

RUN chmod -R 555 /var/www/html /var/www/php-private

USER nobody
