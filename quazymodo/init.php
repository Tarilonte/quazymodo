<?php

// Start the session
session_start();

// Composer autoload
require '../vendor/autoload.php';

// initialize Dotenv
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../app");
$dotenv->load();

// Load quazymodo and session
require '../app/session.php';

// Initialize AltoRouter and load routes
$router = new AltoRouter();
require '../app/routes.php';

// Initialize anti-xss
$antiXSS = new \voku\helper\AntiXSS();