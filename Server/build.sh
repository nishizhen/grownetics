#!/usr/bin/env bash

cd /var/www

pip install --upgrade setuptools
mkdocs build
mv site html/webroot/docs
cd html/
mkdir tmp webroot/cache_js webroot/cache_css || true
chmod -R 777 webroot tmp
composer install
bower install
bin/cake asset_compress build

BUILD_DATE=`date +%Y-%m-%d:%H:%M:%S` && echo "<?php return ['BUILD_ID' => '$CI_PIPELINE_ID','BUILD_DATE' => '$BUILD_DATE'];" > config/build_info.php
