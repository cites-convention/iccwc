# ICCWC

The The International Consortium on Combating Wildlife Crime website

## I. Installation

* Clone the repository
* Run `composer install`
* Create a new database
* Copy `web/sites/example/settings.local.php` to `web/sites/default/settings.local.php` and customize caching settings, database credentials, SOLR connection, mail server connection (if needed)
* Copy `example.robo.yml` to `robo.yml` and customize `username`, `password`, `admin_username` - ask the project owner if you don't have these
* Create the `web/sites/default/files` folder: `mkdir web/sites/default/files`
* Properly set files folder permissions `sudo chown -R YOURUSER:www-data web/sites/default/files && sudo chmod 02775 web/sites/default/files`
* Run `./install.sh`

## II. Drupal development

* Set ticket status to Under Work
* Pull the latest version of the `main` branch
* Create a new branch `XXXXX-issue-description` where XXXXX is the issue number
* Run `composer install`
* Run `./install.sh`
* Code
* Commits should have messages like `refs #XXXXX - Fixed a white screen of death on /front`
* Create a pull request
* Update ticket status, set spent time, add a link to the pull request and testing instructions

## III. Theme development

See theme README.md :)

## IIII. Updating Drupal Core

`composer update "drupal/core-*" --with-all-dependencies`
