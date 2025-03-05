FROM php:7.4-fpm

# install required packages
RUN apt-get update \
    && apt-get install -y \
    libssl-dev \
    zlib1g-dev \
    libicu-dev \
    libonig-dev \
    g++ \
    libgmp-dev \
    libmcrypt-dev \
    git \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libzip-dev \
    zip \
    unzip \
    libxml2-dev \
    unixodbc \
    unixodbc-dev \
    odbcinst \
    gnupg2

RUN docker-php-ext-install zip mbstring pdo pdo_mysql
RUN docker-php-ext-configure intl && docker-php-ext-install intl
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && docker-php-ext-install gd

    
# configure php.ini
RUN echo "date.timezone = UTC" >> /usr/local/etc/php/conf.d/php.ini \
    && echo "memory_limit = 2G" >> /usr/local/etc/php/conf.d/php.ini \
    # printf "; priority=20\nextension=sqlsrv.so\n" > /usr/local/etc/php/conf.d/sqlsrv.ini && \
    # printf "; priority=30\nextension=pdo_sqlsrv.so\n" > /usr/local/etc/php/conf.d/pdo_sqlsrv.ini && \
#    && echo "extension=pdo_sqlsrv.so" >> /usr/local/etc/php/conf.d/php.ini \
#    && echo "extension=sqlsrv.so" >> /usr/local/etc/php/conf.d/php.ini

#   RUN echo "extension=pdo_sqlsrv.so" >> `php --ini | grep "Scan for additional .ini files" | sed -e "s|.*:\s*||"`/30-pdo_sqlsrv.ini \
#    && echo "extension=sqlsrv.so" >> `php --ini | grep "Scan for additional .ini files" | sed -e "s|.*:\s*||"`/30-sqlsrv.ini \
#    && apt-get clean

RUN ln -sf /bin/bash /bin/sh

# install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer config -g github-oauth.github.com c318462c8a5d70fa241bdb5776524812910b95ec \
    && export COMPOSER_PROCESS_TIMEOUT=600
