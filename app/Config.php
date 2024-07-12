<?php
use Tracy\Debugger;

/*
| App Configuration
|------------------
| This file contains the configuration settings for your application.
|*/ 

/*
| Tracy Debugger
|------------------
|*/

// Set the environment for the debugger according to the APP_ENV variable
$debuggerEnv = $_ENV["APP_ENV"] === 'production' ? true : false;
// Set the directory for the debugger logs
$tracyLogDir = __DIR__ . '/writable/tracy/';
// Enable the debugger
Debugger::enable($debuggerEnv, $tracyLogDir);
// Set the error template
Debugger::$errorTemplate = __DIR__ . '/components/templates/page/500.html';
// Set the strict mode
Debugger::$strictMode = false;
// Set the show bar
Debugger::$showBar = true;

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
Quazymodo\Functions\rateLimit();