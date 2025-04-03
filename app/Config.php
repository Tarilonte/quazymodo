<?php

/*
| App Configuration
|------------------
| This file contains the configuration settings for your application.
|*/ 

/*
| Session Configuration
|------------------
|*/
if ($_ENV['APP_SESSION_ENABLE'] == 1) {
  session_start([ 
    'cookie_lifetime' => 0,
    'cookie_path' => '/',
    'cookie_secure' => false,
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict',
    'sid_length' => 22,
    'use_strict_mode' => true,
    'referer_check' => $_SERVER['HTTP_HOST'],
  ]);
}

/*
| Tracy Debugger
|------------------
|*/
if ($_ENV['APP_TRACY_ENABLE'] == 1) {
  // Set the environment for the debugger according to the APP_ENV variable
  $tracyMode = $_ENV["APP_ENV"] === 'production' ? true : false;
  // Set the directory for the debugger logs
  $tracyLogDir = __DIR__ . '/writable/tracy/';
  // Enable the debugger
  Tracy\Debugger::enable($tracyMode, $tracyLogDir);
  // Set the error 500 page
  //Tracy\Debugger::$errorTemplate = __DIR__ . '/components/templates/pages/500.html';
  // Set the strict mode
  Tracy\Debugger::$strictMode = true;
  // Set the show bar
  Tracy\Debugger::$showBar = true;
}

/*
| timezone and locale
|------------------
|*/ 
date_default_timezone_set($_ENV['APP_TIMEZONE']);
setlocale(LC_ALL, $_ENV['APP_LOCALE']);


/*
| Security
|------------------
|*/ 

// Set CSRF token
Quazymodo\Functions\setCsrf();

// Apply rate limit
$userIp = \Quazymodo\Functions\getClientIp($request);
Quazymodo\Functions\rateLimit($userIp);