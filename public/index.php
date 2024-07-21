<?php

// Initialize quazymodo
require '../quazymodo/init.php';

// handle the request
try {
  $response = $router->dispatch($request);
} catch (Exception $e) {
  if (method_exists($e, 'getStatusCode') && $e->getStatusCode() == 404) {
    $controller = new Controller\_404Controller();
    $response = $controller->index($request);
  }
}

// send the response to the browser
\Quazymodo\Functions\emit($response);
