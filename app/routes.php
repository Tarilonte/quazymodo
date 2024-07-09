<?php

//$router->middleware(new Middleware\RequestLoggerMiddleware());

// map a route
$router->map('GET', '/', 'Controller\HomeController::index');
$router->map('GET', '/test/{method}', 'Controller\Test\TestController::index');