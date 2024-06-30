<?php

// Composer autoload
require '../vendor/autoload.php';

// initialize Dotenv
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../app");
$dotenv->load();

// Load quazymodo and session
require '../core/functions.php';
require '../app/session.php';
require '../core/quazymodo.php';

// Load the routes
$router = new AltoRouter();
require '../app/routes.php';