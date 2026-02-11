<?php

/*
 * Routes entrypoint.
 *
 * This file centralizes router bootstrap concerns and delegates
 * actual route registrations to context-specific files.
 */

$router = Quazymodo\App::getRouter();

// Apply global middleware only when rate limit is enabled.
if (RATE_LIMIT_REQUESTS > 0) {
  $router->middleware(middleware: new Middleware\RateLimitMiddleware());
}

require_once __DIR__ . '/web.php';
require_once __DIR__ . '/api.php';
require_once __DIR__ . '/test.php';
require_once __DIR__ . '/dev.php';
