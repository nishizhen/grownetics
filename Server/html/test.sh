#!/usr/bin/env sh

export CHATROOM_ID='2o59pXTMLoCjfhnCr'
export TIMEZONE='America/Denver'
export DB_NAME=grownetics
export DB_TEST_NAME=grownetics_test
export DB_HOSTNAME=appdb
export DB_USER=grownetics
export DB_PASS=grownetics
export DEV=1
export ENVIRONMENT=test
export REDIS_HOSTNAME=redis


/var/www/html/bin/cake migrations seed --source DemoSeeds;

/var/www/html/vendor/bin/phpunit $*
