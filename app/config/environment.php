<?php

/*
 * Runtime environment resolver.
 *
 * Intencao: preservar o contrato publico de APP_ENV enquanto permite um
 * override temporario por sessao antes dos servicos consumirem a constante.
 */

$allowedEnvironments = ['development', 'production'];
$sessionEnvironment = $_SESSION['app-environment'] ?? null;

if (is_string($sessionEnvironment) && in_array(needle: $sessionEnvironment, haystack: $allowedEnvironments, strict: true)) {
  define(constant_name: 'APP_ENV', value: $sessionEnvironment);
} else {
  define(constant_name: 'APP_ENV', value: APP_DEFAULT_ENV);
}
