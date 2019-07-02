#!/usr/bin/env ash

cd /var/www/html
sudo mkdir webroot/cache_js webroot/cache_css
sudo chmod -R 777 webroot/cache_js webroot/cache_css
bower install

set -e
bin/cake asset_compress build || true

