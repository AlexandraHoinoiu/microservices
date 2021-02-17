#!/bin/bash

INSTANCE_HOSTNAME=(`hostname`)

# Set the hostname in the Apache vhost as server name
# It must match the certificate
sed -i "s@INSTANCE_HOSTNAME@${INSTANCE_HOSTNAME}@" /etc/apache2/sites-available/000-default.conf

# Set the storage permissions to be able to write the key
mkdir -p $APP_STORAGE_PATH/app/public
mkdir -p $APP_STORAGE_PATH/cache
mkdir -p $APP_STORAGE_PATH/framework/cache/data
mkdir -p $APP_STORAGE_PATH/framework/sessions
mkdir -p $APP_STORAGE_PATH/framework/testing
mkdir -p $APP_STORAGE_PATH/framework/views
mkdir -p $APP_STORAGE_PATH/keys
mkdir -p $APP_STORAGE_PATH/logs
mkdir -p $APP_STORAGE_PATH/session

chown -R www-data:www-data $APP_STORAGE_PATH
chmod -R 777 $APP_STORAGE_PATH
ln -s /etc/apache2/conf-available/mpm.conf /etc/apache2/conf-enabled/mpm.conf
exec "$@"
