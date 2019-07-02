#!/usr/bin/env bash

if [ "$BACNET_URL" == "" ]; then
    echo "No BACnet information found. Exiting."
else
    echo "Give MariaDB image a chance to spin up."
    sleep 30
    /var/www/html/bin/cake bacnet
fi