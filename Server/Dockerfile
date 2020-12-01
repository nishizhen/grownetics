FROM php:7.1-apache

LABEL maintainer "Emilien Kenler <hello@emilienkenler.com>"

RUN apt-get update && apt-get install -y git libpq-dev libmcrypt-dev zlib1g-dev libicu-dev mysql-client g++ graphviz restic wget vim && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install pdo_pgsql pdo_mysql mbstring mcrypt zip sockets intl bcmath

RUN curl -o /usr/local/bin/composer https://getcomposer.org/composer.phar && \
	chmod +x /usr/local/bin/composer

RUN pecl install xdebug-stable

RUN pecl install apcu-stable

RUN echo "zend_extension=/usr/local/lib/php/extensions/no-debug-non-zts-20160303/xdebug.so" > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.default_enable = 1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.remote_enable = 1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.remote_handler = dbgp" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.start_with_request = 1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.remote_connect_back = 1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.remote_port = 9000" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.remote_host = host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.profiler_enable=0" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.profiler_enable_trigger=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.profiler_output_dir=\"/tmp\"" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN echo "zend_extension=/usr/local/lib/php/extensions/no-debug-non-zts-20160303/opcache.so" > /usr/local/etc/php/conf.d/opcache.ini

RUN echo "extension=apcu.so" > /usr/local/etc/php/conf.d/apcu.ini && \
    echo "apc.enable_cli=1" >> /usr/local/etc/php/conf.d/apcu.ini

RUN echo "realpath_cache_size=4096k" > /usr/local/etc/php/conf.d/tuning.ini && \
    echo "realpath_cache_ttl=300" >> /usr/local/etc/php/conf.d/tuning.ini

RUN echo "date.timezone = \"UTC\"" >> /usr/local/etc/php/conf.d/timezone.ini

# Add apache config to enable .htaccess and do some stuff you want
COPY apache_default /etc/apache2/sites-available/000-default.conf
COPY php.ini /etc/php/7.0/apache2/php.ini

# Enable mod rewrite and listen to localhost
RUN a2enmod rewrite && \
	echo "ServerName localhost" >> /etc/apache2/apache2.conf

RUN wget https://github.com/sass/dart-sass/releases/download/1.15.2/dart-sass-1.15.2-linux-x64.tar.gz && \
	tar -xvzf dart-sass-1.15.2-linux-x64.tar.gz && \
	mv dart-sass/* /usr/local/bin/

# Install Redis
ENV PHPREDIS_VERSION 3.0.0

RUN mkdir -p /usr/src/php/ext/redis \
    && curl -L https://github.com/phpredis/phpredis/archive/$PHPREDIS_VERSION.tar.gz | tar xvz -C /usr/src/php/ext/redis --strip 1 \
    && echo 'redis' >> /usr/src/php-available-exts \
    && docker-php-ext-install redis

################################################################
# Example, deploy a default CakePHP 3 installation from source #
################################################################

ADD html/ /var/www/html

RUN mkdir /var/www/html/webroot/cache_js /var/www/html/webroot/cache_css

# Set workdir (no more cd from now)
WORKDIR /var/www/html

# Composer install application
RUN composer -n install

RUN bin/cake asset_compress build

# Copy the app.php file
RUN sed -i -e "s/'php',/env('SESSION_DEFAULTS', 'php'),/" config/app.php  && \
	chgrp -R www-data logs tmp && \
	chmod -R g+rw logs tmp 

####################################################
# Expose port and run Apache webserver             #
####################################################

ENV VERSION v0.1

EXPOSE 80
CMD ["/usr/sbin/apache2ctl", "-DFOREGROUND"]

