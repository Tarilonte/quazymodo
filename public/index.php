<?php

// Initialize quazymodo
require '../quazymodo/init.php';

// handle the request
try {
  $response = $router->dispatch($request);
} catch (\Throwable $e) {
  // throw the exception if in development mode
  if ($_ENV['APP_ENV'] === 'development') {
    throw $e; // deixa o Tracy capturar
  }

  $statusCode = method_exists($e, 'getStatusCode') 
      ? $e->getStatusCode() 
      : (is_int($e->getCode()) && $e->getCode() !== 0 ? $e->getCode() : 500);  
  $controller = new Controller\ErrorController();
  $response = $controller->handle($request, $statusCode);
}

// send the response to the browser
\Quazymodo\Functions\emit($response);