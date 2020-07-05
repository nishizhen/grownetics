#!/usr/bin/env sh

curl -i -XPOST http://$INFLUX_HOST:8086/query --data-urlencode "q=CREATE DATABASE telegraf"

curl -i -XPOST http://$INFLUX_HOST:8086/query --data-urlencode "q=CREATE DATABASE sensor_data"

curl -i -XPOST http://$INFLUX_HOST:8086/query --data-urlencode "q=CREATE DATABASE system_events"

curl -i -XPOST http://$INFLUX_HOST:8086/query --data-urlencode "q=CREATE DATABASE user_actions"

curl -i -XPOST http://$INFLUX_HOST:8086/query --data-urlencode "q=CREATE DATABASE faker_data"

curl -i -XPOST http://$INFLUX_HOST:8086/query --data-urlencode "q=CREATE DATABASE integration_data"

mkdir webroot/cache_js webroot/cache_css
mkdir -p tmp/cache tmp/sessions
chmod -R 777 tmp/

composer install

mysql -happdb -uroot -pgrownetics -e 'create database if not exists grownetics_test; grant all privileges on grownetics_test.* to grownetics;';

/var/www/html/bin/cake migrations migrate
/var/www/html/bin/cake migrations seed
/var/www/html/bin/cake migrations seed --source AclSeeds
/var/www/html/bin/cake asset_compress build

bin/cake cache clear_all

curl -i -XPOST "http://$INFLUX_HOST:8086/write?db=system_events" --data-binary "ran_seed value=1"
