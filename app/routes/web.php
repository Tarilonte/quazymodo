<?php

/*
 * Web routes for public pages and form submissions.
 */

// Apply shared web middleware at registration time for full scope coverage.
$mapWebRoute = static function (
  string|array $method,
  string $path,
  callable|array|string $handler,
) use ($router) {
  $route = $router->map(
    method: $method,
    path: $path,
    handler: $handler,
  );

  $route->middleware(middleware: new Middleware\CsrfMiddleware());

  return $route;
};

// Home page.
$mapWebRoute(method: 'GET', path: '/', handler: 'Controller\HomeController::index');

// Local-only persistent APP_ENV toggle.
$mapWebRoute(method: 'GET', path: '/changeRuntime', handler: 'Controller\ChangeRuntimeController::toggle');
