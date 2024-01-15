@echo off
title %0
cd /d %~dp0
php -c %~dp0php/php.ini -S 127.0.0.1:8000 -t %~dp0wwwroot
PAUSE
