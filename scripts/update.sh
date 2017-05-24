#!/bin/bash

cd www
git pull
rm -rf var/cache/*
php composer.phar install
npm install
php bin/console cache:clear --env=prod
gulp
chown -R www-data:www-data .
