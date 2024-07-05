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

// Initialize request object
$request = Laminas\Diactoros\ServerRequestFactory::fromGlobals(
  $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
);

// Initialize PHPLeague Route and load App Routes
$router = new League\Route\Router;
require '../app/Routes.php';

// Initialize anti-xss
$antiXSS = new \voku\helper\AntiXSS();