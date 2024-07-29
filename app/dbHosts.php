<?php

/**
 * Database hosts configuration
 * 
 * This file contains the configuration for the database hosts.
 * 
 */

return [
  $_ENV['DB_ALIAS'] => [
    'type' => $_ENV['DB_TYPE'],
    'host' => $_ENV['DB_HOST'],
    'port' => $_ENV['DB_PORT'],
    'username' => $_ENV['DB_USER'],
    'password' => $_ENV['DB_PASS']
  ]
];