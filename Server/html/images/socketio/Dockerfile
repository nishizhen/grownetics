FROM zzrot/alpine-node
MAINTAINER Vo Duy Tuan <tuanmaster2012@gmail.com>

RUN apk add --update \
    supervisor \
  && rm -rf /var/cache/apk/*

ADD supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN /bin/mkdir -p /srv/logs

WORKDIR /srv

RUN npm install --silent socket.io express amqplib request
RUN npm dedupe

ADD socket.js /srv/socket.js

EXPOSE 8989

# CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
CMD ["/usr/bin/node", "/srv/socket.js"]

