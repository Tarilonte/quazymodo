<?php

use Quazymodo\ComponentFactory;
use Quazymodo\CSPManager;

return [
  'extends' => '/pages/base/base',
  'css' => [ASSET_ANIMATECSS, 'form-login.css'],
  'js' => ASSET_HTMX,
  'inserts' => [
    "body" => [
      componentFactory::Plugin("/plugins/navbar/navbar-01"),
      componentFactory::Template("/pages/test-pages/htmx/htmx"),
    ],
    'nonce' => CSPManager::getNonce(),
  ],
];