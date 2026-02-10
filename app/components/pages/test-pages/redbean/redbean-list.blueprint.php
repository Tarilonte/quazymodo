<?php

use Quazymodo\ComponentFactory;

return [
  'extends' => '/pages/base/',
  'js' => ASSET_HTMX,
  'inserts' => [
    'page-title' => 'Lista RedBean',
    'body' => [
      ComponentFactory::Plugin('/plugins/toastComponent/'),
      ComponentFactory::Template(
        '/pages/test-pages/redbean/redbean-list'
        ),
    ]
  ]
];
