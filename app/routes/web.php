<?php

/*
 * Web routes for public pages and form submissions.
 */

// Home page.
$router->map(method: 'GET', path: '/', handler: 'Controller\HomeController::index');

// Local-only persistent APP_ENV toggle.
$router->map(method: 'GET', path: '/changeRuntime', handler: 'Controller\ChangeRuntimeController::toggle');
