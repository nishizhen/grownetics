FROM nginx:stable

RUN apt-get update \
    && apt-get -y install vim

ADD images/nginx/conf.d/growdash.conf /etc/nginx/conf.d/default.conf
ADD images/nginx/conf.d/nginx_status.conf /etc/nginx/conf.d/nginx_status.conf
ADD images/nginx/nginx.conf /etc/nginx/nginx.conf
ADD webroot /var/www/html/webroot