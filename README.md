# ICCWC

The "International Consortium on Combating Wildlife Crime" website

## I. Prerequisites

* PHP 8.1.6+ (see https://www.drupal.org/node/3295154)
* MySQL 5.7.8+ / MariaDB 10.3.7+
* Apache / NGINX
* Composer (https://getcomposer.org)
* NVM (https://github.com/nvm-sh/nvm)
* Node.js 16 (run `nvm use 16`)

## II. Project setup

* Clone the repository
* Create a new database
* Create a new virtual host pointing to the web folder of this project
* Update your `/etc/hosts` file accordingly
* Run `composer install`
* Copy `web/sites/example.settings.local.php` to `web/sites/default/settings.local.php` and customize database credentials.
* Copy `example.robo.yml` to `robo.yml` and customize `username`, `password` and `admin_username`
* (optional) Copy `drush/sites/example.self.site.yml` to `drush/sites/self.site.yml` add configure the ssh user.

## III. Installation

* Run `./install.sh`

## IV. Development

Please make sure you are familiar with:
* Working with helpdesk: https://drupal.eaudeweb.ro/docs/use/helpdesk
* Our GIT workflow: https://drupal.eaudeweb.ro/docs/development-guide/git-workflow

## V. Theme development

See theme README.md :)

## VI. Updating Drupal Core

`composer update "drupal/core-*" --with-all-dependencies`

## VII. Issues with database import

Depending on your MySQL/MariaDB version, you might have the following error when importing the database dump: `ERROR 1273 (HY000) at line 25: Unknown collation: 'utf8mb4_0900_ai_ci'`. A workaround for this is to run `./old-mysql-install.sh` instead of `./install.sh`
