#!/bin/sh
set -e
mkdir -p /var/www/html/storage/uploads
if [ ! -e /var/www/html/public/uploads ]; then
  ln -s /var/www/html/storage/uploads /var/www/html/public/uploads
fi
echo "OK"
