FROM code.cropcircle.io:4567/grownetics/grownetics/php:latest

ADD . /var/www/html
ADD images/growdash/growdash.ini  /usr/local/etc/php/conf.d/growdash.ini
RUN mkdir -m 0777 /var/www/html/webroot/uploads; exit 0