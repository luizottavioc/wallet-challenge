#!/bin/sh

composer install

php -r "file_exists('.env') || copy('.env.example', '.env');"
php bin/hyperf.php server:watch
