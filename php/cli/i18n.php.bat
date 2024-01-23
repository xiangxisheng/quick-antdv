echo off
cd /d %~dp0
cls
for %%i in (%0) do (set "name=%%~ni") 
php %name%
pause
