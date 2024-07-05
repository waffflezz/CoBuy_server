FROM php:8.3-fpm

ENV APP_HOME /var/www/html

ADD ./docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf

RUN docker-php-ext-install pdo pdo_mysql

WORKDIR $APP_HOME

USER root

COPY ./src/ $APP_HOME/
