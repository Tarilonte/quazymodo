<?php

/*
| App Configuration
|------------------
| This file contains the configuration settings for your application.
|*/

// APP VARIABLES AND CREDENTIALS
  
// APP ENVIRONMENT
const APP_ENV = "development"; // development, production
const APP_URL = "https://localhost:8443";
const APP_NAME = "Quazymodo";
const APP_TIMEZONE = "America/Sao_Paulo";
const APP_LOCALE = "pt_BR.utf8";  
const APP_SESSION_ENABLE = 1; // 0 = disabled, 1 = enabled
const APP_CSP_ENABLED = 1; // CSP headers: 0 = disabled, 1 = enabled 

// REQUEST RATE LIMITING
const RATE_LIMIT_REQUESTS = 10; // default requests per period
const RATE_LIMIT_PERIOD = 60; // default period in seconds
const RATE_LIMIT_DB_PATH = __DIR__ . '/writable/db/rate_limit.sqlite';
const TRUSTED_PROXIES = [];
const RATE_LIMIT_POLICIES = [
  'POST /User/processLoginForm' => ['requests' => 10, 'period' => 60],
  'POST /User/processRegistrationForm' => ['requests' => 15, 'period' => 60],
  'POST /api/cep/lookup' => ['requests' => 30, 'period' => 60],
];

// BATABASE CREDENTIALS
const DB = [
  'default' => [
    'type' => 'mysql',
    'host' => '172.26.16.1',
    'port' => '3306',
    'username' => 'root',
    'password' => 'root',  
  ]
];

//ASSETS
const ASSET_JQUERY = "https://code.jquery.com/jquery-4.0.0.min.js";
const ASSET_ANIMATECSS = 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css';
const ASSET_HTMX = "https://cdn.jsdelivr.net/npm/htmx.org@4.0.0-alpha6/dist/htmx.min.js";

// EXTERNAL SERVICES CREDENTIALS
// CONFIGURAÇÕES DE EMAIL
const MAILBABY_API_KEY = "8D3ZkbB7djvEuV1AOoIGKeg7vBgZDRGvUoCeZQtrV4VyloTN74V9vedGHcyUhainkjzzJg6eNzwTSql";

// CREDENCIAIS DO GOOGLE
const GOOGLE_OAUTH_CLIENT_ID = "101268693338-3ji0ij0";


// SESSION CONFIGURATION
if (APP_SESSION_ENABLE == 1) {
  session_start([ 
    'cookie_lifetime' => 0,
    'cookie_path' => '/',
    'cookie_secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict',
    'use_strict_mode' => true
  ]);
}

/*
| timezone and locale
|------------------
|*/ 
  date_default_timezone_set(APP_TIMEZONE);
  setlocale(LC_ALL, APP_LOCALE);

/*
| Tracy Debugger
|------------------
|*/
  if (APP_ENV == 'development') {
    // Set the environment for the debugger according to the APP_ENV variable
    $tracyMode = Tracy\Debugger::Development;
    // Set the directory for the debugger logs
    $tracyLogDir = __DIR__ . '/writable/tracy/';
    // Enable the debugger
    Tracy\Debugger::enable($tracyMode, $tracyLogDir);
    // Set the strict mode
    Tracy\Debugger::$strictMode = true;
    // Set the show bar
    Tracy\Debugger::$showBar = true;
  }
