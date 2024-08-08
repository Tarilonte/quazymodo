<?php

namespace Quazymodo\Functions;

/*
|------------------------------------
| APP FUNCTIONS
|------------------------------------
|
| This section contains the functions used by the application.
|
|*/

function setCsrf(): void 
{
  if (!isset($_SESSION["csrf-token"])){
  $_SESSION["csrf-token"] = bin2hex(random_bytes(16));
  }
}

function isCsrfValid(): bool 
{
  if (!isset($_SESSION['csrf-token']) || !isset($_POST['csrf-token'])) {
  return false;
  }
  if ($_SESSION['csrf-token'] != $_POST['csrf-token']) {
  return false;
  }
  if ($_SESSION['csrf-token'] === $_POST['csrf-token']) {
  return true;
  }
  return false;  
}

function rateLimit(): void
{
    $limit = $_ENV['RATE_LIMIT_REQUESTS'];
    $period = $_ENV['RATE_LIMIT_PERIOD'];

    if (!isset($_SESSION['rate_limit'])) {
        $_SESSION['rate_limit'] = [];
    }

    $time = time();
    $_SESSION['rate_limit'] = array_filter($_SESSION['rate_limit'], function ($timestamp) use ($time, $period) {
        return ($time - $timestamp) < $period;
    });

    if (count($_SESSION['rate_limit']) >= $limit) {
        header('HTTP/1.1 429 Too Many Requests');
        echo "Rate limit exceeded. Please wait a few seconds and try again.";
        exit;
    }

    $_SESSION['rate_limit'][] = $time;
}

function getClientIp(\Psr\Http\Message\ServerRequestInterface $request): string
{
  $serverParams = $request->getServerParams();

  $ipHeaders = [
    'HTTP_CLIENT_IP',
    'HTTP_X_FORWARDED_FOR',
    'REMOTE_ADDR'
  ];

  foreach ($ipHeaders as $header) {
    if (isset($serverParams[$header])) {
      return $serverParams[$header];
    }
  }

  return 'UNKNOWN';
}

function recursiveArraySearch($array, $keyToFind): ?string
{
  foreach ($array as $key => $value) {
    if ($key === $keyToFind) {
      if (is_array($value)) {
        return print_r($value,true);
      }
      return $value;
    } elseif (is_array($value)) {
      $result = recursiveArraySearch($value, $keyToFind);
      if ($result !== null) {
        return $result;
      }
    }
  }
  return null;
}

function emit(\Psr\Http\Message\ResponseInterface $response): void
{
  http_response_code($response->getStatusCode());
  foreach ($response->getHeaders() as $name => $values) {
    foreach ($values as $value) {
      header(sprintf('%s: %s', $name, $value), false);
    }
  }
  echo $response->getBody();
  exit;
}

/*
|------------------------------------
| DEVELOPMENT FUNCTIONS
|------------------------------------
|
| This section contains the functions used for development purposes.
|
|*/

// TODO: Estudar a possibilidade de excluir essas funções

function show(mixed $stuff, string $nome = "Não informado"): void {
  if (is_array($stuff)) {
  $stuff = escapeArrayValues($stuff);
  } else {
  $stuff = decorateStuff($stuff);
  }

  echo "<div class='mockup-code'><pre class='p-6'><code>";
  echo "[Showing: $nome]" . PHP_EOL;
	print_r($stuff);
  echo "</code></pre></div>";
}

function escapeArrayValues($array): array {
  foreach ($array as $key => $value) {
    // Se o valor for um array, chama a função recursivamente
    if (is_array($value)) {
      $array[$key] = escapeArrayValues($value);
    } else {
      // Aplica htmlspecialchars ao valor
      $array[$key] = decorateStuff($value);
    }
  }
  return $array;
}

function decorateStuff(mixed $stuff): string {
  $stuffType = gettype($stuff);
  $stuff = htmlspecialchars($stuff);
  $stuff = "$stuff ( $stuffType:".strlen($stuff)." )";
  return $stuff;
}