<?php

$router = Quazymodo\App::getRouter();

$router->middleware(new Middleware\RateLimitMiddleware());

// map a route
$router->map('GET', '/', 'Controller\HomeController::index');
$router->map('GET', '/test/{test:.*}', 'Controller\Test\TestController::index');
$router->map('GET', '/coinchange2', 'Controller\coinchange2::index');

$router->map('GET', '/login', 'Controller\userController::showLoginForm');
$router->map('POST', '/User/processLoginForm', 'Controller\userController::processLoginForm');

$router->map('GET', '/phpinfo', 'Controller\Test\PHPInfoController::index');


$router->map('GET', '/adminer', function () {
  require __DIR__ . '/../quazymodo/adminer.php';
  die();
});

$router->map('POST', '/adminer', function () {
  require __DIR__ . '/../quazymodo/adminer.php';
  die();
});

$router->map('GET', '/sse/hora', 'Controller\sse\HoracertaSseControler::index');