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

function rateLimit(string $clientIp): void
{
    $limit = $_ENV['RATE_LIMIT_REQUESTS'];
    $period = $_ENV['RATE_LIMIT_PERIOD'];

    if (!isset($_SESSION['rate_limit'])) {
        $_SESSION['rate_limit'] = [];
    }

    if (!isset($_SESSION['rate_limit'][$clientIp])) {
        $_SESSION['rate_limit'][$clientIp] = [];
    }

    $time = time();
    $_SESSION['rate_limit'][$clientIp] = array_filter($_SESSION['rate_limit'][$clientIp], function ($timestamp) use ($time, $period) {
        return ($time - $timestamp) < $period;
    });

    if (count($_SESSION['rate_limit'][$clientIp]) >= $limit) {
        header('HTTP/1.1 429 Too Many Requests');
        echo "Rate limit exceeded. Please wait a few seconds and try again.";
        exit;
    }

    $_SESSION['rate_limit'][$clientIp][] = $time;
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
    // 1. Define status code
    http_response_code($response->getStatusCode());

    // 2. Envia headers (incluindo HSTS se HTTPS)
    $isHttps = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    foreach ($response->getHeaders() as $name => $values) {
        $replace = ($name === 'Set-Cookie') ? false : true;
        foreach ($values as $value) {
            header("$name: $value", $replace);
        }
    }
    
    // 3. Adiciona headers de segurança condicionais (exceto para arquivos binários)
    if (!str_contains($response->getHeaderLine('Content-Type'), 'application/octet-stream')) {
        if ($isHttps && $_ENV['APP_ENV'] === 'production') {
            header("Strict-Transport-Security: max-age=63072000; includeSubDomains");
        }
        header("X-Content-Type-Options: nosniff");
        header("Referrer-Policy: strict-origin-when-cross-origin");
        header_remove('X-Powered-By');
    }

    // 4. Stream do corpo da resposta (eficiente para grandes conteúdos)
    $stream = $response->getBody();
    $stream->rewind();
    while (!$stream->eof()) {
        echo $stream->read(8192); // Buffer de 8KB
    }

    // 5. Encerra a execução (opcional, mas recomendado para evitar saída acidental)
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