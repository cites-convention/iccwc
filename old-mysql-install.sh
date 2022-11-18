#!/bin/bash

composer install
./vendor/bin/robo sql:download database.sql.gz
zcat database.sql.gz > database.sql
sed -i 's/utf8mb4_0900_ai_ci/utf8mb4_unicode_ci/g' database.sql
./vendor/bin/drush sql-drop -y
./vendor/bin/drush sqlc < database.sql
rm database.sql
rm database.sql.gz
./vendor/bin/robo site:update
./vendor/bin/robo site:develop
