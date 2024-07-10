<?php

// Start the session
session_start( [ 
  'cookie_lifetime' => 0,
  'cookie_path' => '/',
  'cookie_secure' => false,
  'cookie_httponly' => true,
  'cookie_samesite' => 'Strict',
  'sid_length' => 96,
  'sid_bits_per_character' => 5,
  'use_strict_mode' => true,
  'referer_check' => $_SERVER['HTTP_HOST'],
] );

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