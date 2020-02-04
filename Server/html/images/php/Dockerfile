FROM php:7.2-fpm

RUN apt-get update \
    && apt-get -y install curl gnupg \
    && curl -sL https://deb.nodesource.com/setup_6.x | bash - \
    && apt-get -y install \
    autoconf \
    default-mysql-client \
    g++ \
    iputils-ping \
    libicu-dev \
    libcurl4-gnutls-dev \
    libmcrypt-dev \
    libpng-dev \
    libxml2-dev \
    make \
    nodejs \
    npm \
    ruby \
    ruby-dev \
    unzip \
    vim \
    wget \
    zip \
    && gem install -n /usr/local/bin sass \
    && npm install -g bower grunt-cli \
    && echo '{ "allow_root": true }' > /root/.bowerrc \
    && docker-php-source extract \
    && docker-php-ext-install curl gd json mbstring bcmath dom intl pdo_mysql soap sockets \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && rm -rf /tmp/*

ENV PHPREDIS_VERSION 3.0.0

RUN mkdir -p /usr/src/php/ext/redis \
    && curl -L https://github.com/phpredis/phpredis/archive/$PHPREDIS_VERSION.tar.gz | tar xvz -C /usr/src/php/ext/redis --strip 1 \
    && echo 'redis' >> /usr/src/php-available-exts \
    && docker-php-ext-install redis

ENV RESTIC_VERSION=0.9.4
ADD https://github.com/restic/restic/releases/download/v${RESTIC_VERSION}/restic_${RESTIC_VERSION}_linux_amd64.bz2 /
RUN bzip2 -d /restic_${RESTIC_VERSION}_linux_amd64.bz2 && mv /restic_${RESTIC_VERSION}_linux_amd64 /bin/restic && chmod +x /bin/restic

RUN mkdir -p /mnt/restic /var/spool/cron/crontabs /var/log


COPY images/php/www.conf /usr/local/etc/php-fpm.d/www.conf

#RUN mv $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini

# Override with custom opcache settings
COPY images/php/uploads.ini $PHP_INI_DIR/conf.d/

WORKDIR /var/www/html
