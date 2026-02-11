<?php

/*
 * Test and playground routes used during development.
 */

// Test index and specific test endpoints.
$router->map(method: ['GET', 'POST'], path: '/test', handler: 'Controller\Test\TestController::list');
$router->map(method: ['GET', 'POST'], path: '/test/redbean', handler: 'Controller\Test\TestController::redbean');
$router->map(method: ['GET', 'POST'], path: '/test/redbean/lista', handler: 'Controller\Test\TestController::redbeanList');
$router->map(method: ['GET', 'POST'], path: '/test/{test:.*}', handler: 'Controller\Test\TestController::index');
