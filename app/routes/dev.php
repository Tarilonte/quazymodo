<?php

/*
 * Development-only routes.
 */

// Register debug/admin tools only in development mode.
if (APP_ENV === 'development') {
  // Component showcase page (development-only).
  $router->map(method: 'GET', path: '/exemplos', handler: 'Controller\ExamplesController::index');

  $router->map(method: 'GET', path: '/adminer', handler: 'Controller\Adminer\AdminerController::index');
  $router->map(method: 'POST', path: '/adminer', handler: 'Controller\Adminer\AdminerController::index');
}
