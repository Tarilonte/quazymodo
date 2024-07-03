<?php

// Start the session
session_start();

// Composer autoload
require '../vendor/autoload.php';

// initialize Dotenv
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../app");
$dotenv->load();

// Load app configuration
require '../app/Config.php';

// Initialize AltoRouter and load routes
$router = new AltoRouter();
require '../app/Routes.php';

// Initialize anti-xss
$antiXSS = new \voku\helper\AntiXSS();