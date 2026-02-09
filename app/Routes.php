<?php

$router = Quazymodo\App::getRouter();

if(RATE_LIMIT_REQUESTS > 0){
  $router->middleware(new Middleware\RateLimitMiddleware());
}

// map a route
$router->map('GET', '/', 'Controller\HomeController::index');
$router->map('GET', '/login', 'Controller\UserController::showLoginForm');
$router->map('GET', '/address', 'Controller\UserController::showAddressForm');
$router->map('POST', '/User/processLoginForm', 'Controller\UserController::processLoginForm');
$router->map('POST', '/api/cep/lookup', 'Controller\CepController::lookup');
$router->map('GET', '/register', 'Controller\UserController::showRegistrationForm');
$router->map('POST', '/User/processRegistrationForm', 'Controller\UserController::processRegistrationForm');

//Adminer
$router->map('GET', '/adminer', function () {
  require __DIR__ . '/../adminer.php';
  die();
});

$router->map('POST', '/adminer', function () {
  require __DIR__ . '/../adminer.php';
  die();
});

// testes
$router->map(['GET','POST'], '/test', 'Controller\Test\TestController::list');
$router->map(['GET','POST'], '/test/redbean', 'Controller\Test\TestController::redbean');
$router->map(['GET','POST'], '/test/redbean/lista', 'Controller\Test\TestController::redbeanList');
$router->map(['GET','POST'], '/test/{test:.*}', 'Controller\Test\TestController::index');

// chat
$router->map('GET', '/chat', 'Controller\Chat\LobbyController::index');
$router->map('POST', '/chat/enterLobby', 'Controller\Chat\LobbyController::enterLobby');

// Rotas disponiveis apenas em dev
if (APP_ENV == "development") {
  $router->map('GET', '/info', 'Controller\PHPInfoController::index');
}
