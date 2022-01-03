#!/bin/bash

php artisan queue:clear
php artisan game:chain all

nohup laravel-echo-server start --force & disown
nohup php artisan queue:listen --sleep 0.01 & disown
