#!/bin/sh

file_path=/tmp/setup.lock
if [ -e "$file_path" ]; then
    echo "installed"
else
    echo `date` 1>$file_path
    sh /root/script/setup.sh
fi

sh /root/script/reload.sh
apache2-foreground
