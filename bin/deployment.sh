#!/bin/bash

echo 'Deploying...'
cd /var/www/ohlasky.cz

touch www/maintenance.lock
sleep 2

rm -r temp/cache/*

#chmod -R 0770 /var/www/ohlasky.cz # maybe owner also

composer install --no-dev --no-interaction 2>&1;
#npm install
#bower install
#grunt

php www/index.php orm:schema-tool:update --force

rm -f www/maintenance.lock

echo 'Deployment finished successfully.'