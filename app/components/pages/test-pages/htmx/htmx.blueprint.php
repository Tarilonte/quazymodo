<?php

use Quazymodo\ComponentFactory;
use Quazymodo\CSPManager;

return [
  'extends' => '/pages/base/',
  'css' => [ASSET_ANIMATECSS, 'form-login.css'],
  'js' => ASSET_HTMX,
  'inserts' => [
    "body" => [
      componentFactory::Plugin("/plugins/navbar/"),
      componentFactory::Template("/pages/test-pages/htmx/"),
    ],
    'nonce' => CSPManager::getNonce(),
  ],
];