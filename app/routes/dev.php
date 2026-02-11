<?php

/*
 * Development-only routes.
 */

// Register debug/admin tools only in development mode.
if (APP_ENV === 'development') {
  $router->map(method: 'GET', path: '/info', handler: 'Controller\PHPInfoController::index');
  $router->map(method: 'GET', path: '/adminer', handler: 'Controller\Adminer\AdminerController::index');
  $router->map(method: 'POST', path: '/adminer', handler: 'Controller\Adminer\AdminerController::index');
}
