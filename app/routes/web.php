<?php

/*
 * Web routes for public pages and form submissions.
 */

// Home page.
$router->map(method: 'GET', path: '/', handler: 'Controller\HomeController::index');
