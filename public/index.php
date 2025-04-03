<?php

// Initialize quazymodo
require '../quazymodo/init.php';

// handle the request
try {
  $response = $router->dispatch($request);
} catch (\Throwable $e) {
  $statusCode = method_exists($e, 'getStatusCode') 
      ? $e->getStatusCode() 
      : (is_int($e->getCode()) && $e->getCode() !== 0 ? $e->getCode() : 500);  
  $controller = new Controller\ErrorController();
  $response = $controller->handle($request, $statusCode);
}

// send the response to the browser
\Quazymodo\Functions\emit($response);
