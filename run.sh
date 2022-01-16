#!/bin/sh
set -e
php artisan queue:table
php artisan migrate --force
service nginx restart
php-fpm