@echo off
title %0
cd /d %~dp0
php -c %~dp0php/php.ini -S 0.0.0.0:8000 -t %~dp0wwwroot
PAUSE
