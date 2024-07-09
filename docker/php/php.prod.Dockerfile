FROM php:8.3-fpm

ENV APP_HOME /var/www/html

ENV USERNAME=www-data

ADD ./docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf

RUN docker-php-ext-install pdo pdo_mysql

RUN chown -R ${USERNAME}:${USERNAME} $APP_HOME

WORKDIR $APP_HOME

COPY --chown=${USERNAME}:${USERNAME} ./src/ $APP_HOME/

USER ${USERNAME}