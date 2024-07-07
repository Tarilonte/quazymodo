<?php

$router->middleware(new Middleware\testMiddleware());

// map a route
$router->map('GET', '/', 'Controller\HomeController::index');
$router->map('GET', '/test/{method}', 'Controller\Test\TestController::index');