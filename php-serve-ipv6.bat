@echo off
cd /d %~dp0
for %%i in (%0) do (set "name=%%~ni")
title %name%
php -c %~dp0php/php.ini -S [::]:8000 -t %~dp0wwwroot
PAUSE
