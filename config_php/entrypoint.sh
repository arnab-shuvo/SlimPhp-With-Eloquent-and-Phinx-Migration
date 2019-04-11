#!/usr/bin/env bash

cd app && vendor/bin/phinx migrate
exec php-fpm