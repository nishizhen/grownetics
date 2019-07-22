#!/usr/bin/env sh

export DB_TEST_NAME=grownetics_test

/var/www/html/vendor/bin/phpunit $*
