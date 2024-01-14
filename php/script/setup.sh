#!/bin/bash

#common
apt update

#php-ext pdo_pgsql
apt install -y libpq-dev
docker-php-ext-install pdo_pgsql

cd /etc/apache2/mods-enabled
ln -s ../mods-available/rewrite.load .

sh /root/script/reload.sh
