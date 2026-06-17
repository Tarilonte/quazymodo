<?php

/*
 * Web routes for public pages and form submissions.
 */

// Home page.
$router->map(method: 'GET', path: '/', handler: 'Controller\HomeController::index');

// Temporary environment switch for the current session.
$router->map(method: 'POST', path: '/environment', handler: 'Controller\EnvironmentController::update');
