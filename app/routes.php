<?php

//$router->middleware(new Middleware\RequestLoggerMiddleware());

// map a route

$router->map('GET', '/', 'Controller\HomeController::index');
$router->map('GET', '/test/{test:.*}', 'Controller\Test\TestController::index');
$router->map('GET', '/apporcelanas', 'Controller\ApPorcelanas\HomeController::index');