<?php

//$router->middleware(new Middleware\testMiddleware());

// map a route
$router->map('GET', '/', 'Controller\HomeController::index');
$router->map('GET', '/404', 'Controller\_404Controller::index');