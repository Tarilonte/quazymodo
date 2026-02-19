<?php

/*
 * Web routes for public pages and form submissions.
 */

// Home page.
$router->map(method: 'GET', path: '/', handler: 'Controller\HomeController::index');

// Catalog page.
$router->map(method: 'GET', path: '/catalogo', handler: 'Controller\HomeController::catalogo');

// Catalog v2 page.
$router->map(method: 'GET', path: '/catalogo/v2', handler: 'Controller\HomeController::catalogoV2');

// Theme color preview page.
$router->map(method: 'GET', path: '/tema', handler: 'Controller\HomeController::tema');

// User auth and profile forms.
$router->map(method: 'GET', path: '/login', handler: 'Controller\UserController::showLoginForm');
$router->map(method: 'GET', path: '/register', handler: 'Controller\UserController::showRegistrationForm');
$router->map(method: 'GET', path: '/address', handler: 'Controller\UserController::showAddressForm');
$router->map(method: 'POST', path: '/User/processLoginForm', handler: 'Controller\UserController::processLoginForm');
$router->map(method: 'POST', path: '/User/processRegistrationForm', handler: 'Controller\UserController::processRegistrationForm');

// Chat pages.
$router->map(method: 'GET', path: '/chat', handler: 'Controller\Chat\LobbyController::index');
$router->map(method: 'POST', path: '/chat/enterLobby', handler: 'Controller\Chat\LobbyController::enterLobby');
