<?php declare(strict_types=1);
  
// Composer autoload
require '../vendor/autoload.php';

// Load App Environment Variables
// (They are set in 'app/.env')
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../app");
$dotenv->load();

// Initialize request object
$psr17Factory = new Nyholm\Psr7\Factory\Psr17Factory();
$creator = new Nyholm\Psr7Server\ServerRequestCreator(
  $psr17Factory, // ServerRequestFactory
  $psr17Factory, // UriFactory
  $psr17Factory, // UploadedFileFactory
  $psr17Factory  // StreamFactory
);
$request = $creator->fromGlobals(
  $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
);

// Require App configuration
require '../app/Config.php';

// Initialize PHPLeague Route and load App Routes
$router = new League\Route\Router;
require '../app/Routes.php';

// handle the request
try {
  // Apply rate limit
  $userIp = \Quazymodo\Functions\getClientIp($request);
  Quazymodo\Functions\rateLimit($userIp);

  // Dispatch the request to the router
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
