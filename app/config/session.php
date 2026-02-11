<?php

/*
 * Session runtime bootstrap.
 */

// Initialize session when enabled by configuration.
if (APP_SESSION_ENABLE === 1) {
  session_start([
    'cookie_lifetime' => 0,
    'cookie_path' => '/',
    'cookie_secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict',
    'use_strict_mode' => true,
  ]);
}
