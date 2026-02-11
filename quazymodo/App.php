<?php

namespace Quazymodo;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use League\Route\Router;
use Controller\ErrorController;
use Throwable;

class App{

  private static ServerRequestInterface $request;
  private static ResponseInterface $response;
  private static Router $router;

  static function Run(): void
  {
    self::loadConfig();
    self::initRequest();
    self::initRoutes();
    self::handleRequest();
  }

  private static function initRequest(): void
  {
    $psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory();
    $creator = new \Nyholm\Psr7Server\ServerRequestCreator(
      $psr17Factory,
      $psr17Factory,
      $psr17Factory,
      $psr17Factory
    );
    self::$request = $creator->fromGlobals(
      $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
    );
  }

  private static function loadConfig(): void
  {
    require dirname(__DIR__) . '/app/config/index.php';
  }

  private static function initRoutes(): void
  {
    self::$router = new \League\Route\Router;
    require dirname(__DIR__) . '/app/Routes.php';
  }

  public static function getRouter(): Router
  {
    return self::$router;
  }


  private static function handleRequest(): void
  {
    try {
      self::$response = self::$router->dispatch(self::$request);
    } catch (\Throwable $e) {
      self::handleException($e);
    }
    self::emit();
  }

  private static function handleException(\Throwable $e): void
  {
    if (APP_ENV === 'development') {
      throw $e; // Deixa o Tracy ou outro handler capturar
    }
    $statusCode = method_exists($e, 'getStatusCode')
      ? $e->getStatusCode()
      : (is_int($e->getCode()) && $e->getCode() !== 0 ? $e->getCode() : 500);
    $controller = new ErrorController();
    self::$response = $controller->handle(self::$request, $statusCode);
  }

  private static function emit(): void
  {
      http_response_code(self::$response->getStatusCode());

      self::sendHeaders();
      self::sendBody();

      exit;
  }

  private static function sendHeaders(): void
  {
    $isHttps = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';

    foreach (self::$response->getHeaders() as $name => $values) {
        $replace = ($name === 'Set-Cookie') ? false : true;
        foreach ($values as $value) {
            header("$name: $value", $replace);
        }
    }

    if (!str_contains(self::$response->getHeaderLine('Content-Type'), 'application/octet-stream')) {
        if ($isHttps && (APP_ENV ?? 'production') === 'production') {
            header("Strict-Transport-Security: max-age=63072000; includeSubDomains");
        }
        header("X-Content-Type-Options: nosniff");
        header("Referrer-Policy: strict-origin-when-cross-origin");
        header_remove('X-Powered-By');
    }
  }

  private static function sendBody(): void
  {
    $stream = self::$response->getBody();
    $stream->rewind();
    while (!$stream->eof()) {
        echo $stream->read(8192);
    }
  }

}
