#!/bin/bash

INSTANCE_HOSTNAME=(`hostname`)

# Set the hostname in the Apache vhost as server name
# It must match the certificate
sed -i "s@INSTANCE_HOSTNAME@${INSTANCE_HOSTNAME}@" /etc/apache2/sites-available/000-default.conf

# Set the storage permissions to be able to write the key
mkdir -p /var/www/storage/app/public
mkdir -p /var/www/storage/cache
mkdir -p /var/www/storage/framework/cache/data
mkdir -p /var/www/storage/framework/sessions
mkdir -p /var/www/storage/framework/testing
mkdir -p /var/www/storage/framework/views
mkdir -p /var/www/storage/keys
mkdir -p /var/www/storage/logs
mkdir -p /var/www/storage/session

chown -R www-data:www-data /var/www/storage
chmod -R 777 /var/www/storage
ln -s /etc/apache2/conf-available/mpm.conf /etc/apache2/conf-enabled/mpm.conf
exec "$@"
