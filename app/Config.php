<?php

/*
| App Configuration
|------------------
| This file contains the configuration settings for your application.
|*/

// APP VARIABLES AND CREDENTIALS
  
// APP ENVIRONMENT
const APP_ENV = "development"; // development, production
const APP_URL = "http://quazymodo";
const APP_NAME = "Quazymodo";
const APP_TIMEZONE = "America/Sao_Paulo";
const APP_LOCALE = "pt_BR.utf8";
const APP_SESSION_ENABLE = 1; // 0 = disabled, 1 = enabled
const APP_TRACY_ENABLE = 1; // 0 = disabled, 1 = enabled

// REQUEST RATE LIMITING
const RATE_LIMIT_REQUESTS = 0; // Request Limit (0 = unlimited)
const RATE_LIMIT_PERIOD = 0; // Rate Limit Period (0 = no limit)

// CONTENT SECURITY POLICY HEADERS (CSP)
const APP_CSP_ENABLED = 1; // 0 = disabled, 1 = enabled

// BATABASE CREDENTIALS
const DB = [
  'default' => [
    'type' => 'mysql',
    'host' => 'localhost',
    'port' => '3306',
    'username' => 'root',
    'password' => 'root',  
  ]
];

// EXTERNAL SERVICES CREDENTIALS
  // CONFIGURAÇÕES DE EMAIL
const MAILBABY_API_KEY = "8D3ZkbB7djvEuV1AOoIGKeg7vBgZDRGvUoCeZQtrV4VyloTN74V9vedGHcyUhainkjzzJg6eNzwTSqlt7dKj9VV6534w5Z4lJvJmke4P5vL2zERsdpPLM8cf3cqMjPHt";

// CREDENCIAIS DO GOOGLE
const GOOGLE_OAUTH_CLIENT_ID = "101268693338-3ji0ij09oako9tr02qcg6hhve3a3jkva.apps.googleusercontent.com";


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
  if (APP_TRACY_ENABLE == 1) {
    // Set the environment for the debugger according to the APP_ENV variable
    $tracyMode = APP_ENV === 'production' ? true : false;
    // Set the directory for the debugger logs
    $tracyLogDir = __DIR__ . '/writable/tracy/';
    // Enable the debugger
    Tracy\Debugger::enable($tracyMode, $tracyLogDir);
    // Set the strict mode
    Tracy\Debugger::$strictMode = true;
    // Set the show bar
    Tracy\Debugger::$showBar = true;
  }