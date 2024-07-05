<?php

// Initialize quazymodo
require '../quazymodo/init.php';

// Dispatch the request

try {
  $response = $router->dispatch($request);
} catch (Exception $e) {
  if ($e->getStatusCode() == 404) {
    $controller = new Controller\_404Controller();
    $response = $controller->index($request);
  }
}


// send the response to the browser
(new Laminas\HttpHandlerRunner\Emitter\SapiEmitter)->emit($response);
