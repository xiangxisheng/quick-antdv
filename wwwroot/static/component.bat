@echo off
cd /d %~dp0
for %%i in (%0) do (set "name=%%~ni")
title %name%
php component.php
PAUSE
