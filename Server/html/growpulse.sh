#!/usr/bin/env sh

echo "Give MariaDB image a chance to spin up."
sleep 30

/var/www/html/seed.sh

/var/www/html/bin/cake growpulse