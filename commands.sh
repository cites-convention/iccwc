#!/bin/bash

echo "Running commands before entrypoint"
set -x

cd /var/www/drupal/

cp deploy/prod.robo.yml robo.yml
./vendor/bin/robo site:update

if [ "$ENV" != "prod" ]
then
  echo -e "User-agent: *\nDisallow: /" > web/robots.txt
fi

date -Iseconds > 'web/deployment.html'
