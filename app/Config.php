<?php

/*
| App Configuration
|------------------
| This file contains the configuration settings for your application.
|*/ 


// Disable error reporting in production environment
if ($_ENV['APP_ENV'] === 'production') {
		ini_set('display_errors', 0);
		error_reporting(0);
}

// Set the timezone and locale
date_default_timezone_set($_ENV['APP_TIMEZONE']);
setlocale(LC_ALL, $_ENV['APP_LOCALE']);

// Set CSRF token
Quazymodo\Functions\setCsrf();

// Apply rate limit
Quazymodo\Functions\rateLimit();