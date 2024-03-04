#!/bin/sh

#config-1
cp /root/script/000-default.conf /etc/apache2/sites-enabled/

#reload
service apache2 reload
