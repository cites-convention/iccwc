<?php

/**
 * This file is included very early. See autoload.files in composer.json and
 * https://getcomposer.org/doc/04-schema.md#files
 */

use Dotenv\Dotenv;


/**
 * Load any .env file. See /.env.example.
 */
$dotenv = Dotenv::createUnsafeMutable(__DIR__);
if (file_exists(__DIR__ . '/.env')) {
  $dotenv->safeLoad();
}
