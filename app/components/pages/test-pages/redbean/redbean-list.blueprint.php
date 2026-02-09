<?php

use Quazymodo\ComponentFactory;

return [
  'extends' => '/pages/base/',
  'inserts' => [
    'page-title' => 'Lista RedBean',
    'body' => [
      ComponentFactory::Template(
        '/pages/test-pages/redbean/redbean-list'
        ),
    ]
  ]
];
