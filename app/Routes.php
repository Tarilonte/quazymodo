<?php

$router = Quazymodo\App::getRouter();

if(RATE_LIMIT_REQUESTS > 0){
  $router->middleware(new Middleware\RateLimitMiddleware());
}

// map a route
$router->map('GET', '/', 'Controller\HomeController::index');
$router->map('GET', '/login', 'Controller\userController::showLoginForm');
$router->map('GET', '/address', 'Controller\userController::showAddressForm');
$router->map('POST', '/User/processLoginForm', 'Controller\userController::processLoginForm');
$router->map('POST', '/User/validateCEP', 'Controller\userController::validateCEP');
$router->map('GET', '/register', 'Controller\userController::showRegistrationForm');
$router->map('POST', '/User/processRegistrationForm', 'Controller\userController::processRegistrationForm');

//Adminer
$router->map('GET', '/adminer', function () {
  require __DIR__ . '/../quazymodo/adminer.php';
  die();
});

$router->map('POST', '/adminer', function () {
  require __DIR__ . '/../quazymodo/adminer.php';
  die();
});

// testes
$router->map(['GET','POST'], '/test', 'Controller\Test\TestController::list');
$router->map(['GET','POST'], '/test/{test:.*}', 'Controller\Test\TestController::index');

// chat
$router->map('GET', '/chat', 'Controller\Chat\LobbyController::index');
$router->map('POST', '/chat/enterLobby', 'Controller\Chat\LobbyController::enterLobby');

// Rotas disponiveis apenas em dev
if (APP_ENV == "development") {
  $router->map('GET', '/info', 'Controller\PHPInfoController::index');
}