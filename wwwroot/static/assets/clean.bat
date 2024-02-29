@ECHO OFF
TITLE %0
DEL css\boxicons.min.css
DEL css\reset.min.css
DEL fonts\boxicons.woff2
DEL img\logo.svg
RD /Q /S js\antd
RD /Q /S js\sheetjs
RD /Q /S js\vue
echo The static directory has been clean successfully!
PAUSE
