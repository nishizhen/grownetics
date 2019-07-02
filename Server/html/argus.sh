#!/usr/bin/env bash

if [ "$ARGUS_URL" == "" ]; then
    echo "No Argus integration information found. Exiting."
else
    /var/www/html/bin/cake argus
fi