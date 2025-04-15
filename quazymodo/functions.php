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