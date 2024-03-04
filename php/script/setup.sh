#!/bin/sh

#common
apt update

#php-ext pdo_pgsql
apt install -y libpq-dev
docker-php-ext-install pdo_pgsql

#php-ext pdo_mysql
docker-php-ext-install pdo_mysql

cd /etc/apache2/mods-enabled
ln -s ../mods-available/rewrite.load .

sh /root/script/reload.sh
