FROM code.cropcircle.io:4567/grownetics/grownetics/growdash:latest

# Install Supercronic to run the job as a cronjob.
ENV SUPERCRONIC_URL=https://github.com/aptible/supercronic/releases/download/v0.1.5/supercronic-linux-amd64 \
    SUPERCRONIC=supercronic-linux-amd64 \
    SUPERCRONIC_SHA1SUM=9aeb41e00cc7b71d30d33c57a2333f2c2581a201

RUN curl -fsSLO "$SUPERCRONIC_URL" \
 && echo "${SUPERCRONIC_SHA1SUM}  ${SUPERCRONIC}" | sha1sum -c - \
 && chmod +x "$SUPERCRONIC" \
 && mv "$SUPERCRONIC" "/usr/local/bin/${SUPERCRONIC}" \
 && ln -s "/usr/local/bin/${SUPERCRONIC}" /usr/local/bin/supercronic

#RUN chmod 0644 /etc/crontab
#RUN chmod a+x /etc/Tasknotification.sh
#RUN chmod a+x /var/www/html/src/Shell/TasknotificationShell.php

CMD [ "supercronic", "/etc/crontab" ]
