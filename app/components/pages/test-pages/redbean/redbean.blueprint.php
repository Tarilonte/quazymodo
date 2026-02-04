<?php

use Quazymodo\ComponentFactory;

return [
  'extends' => '/pages/base/',
  'inserts' => [
    'page-title' => 'Teste RedBean',
    'body' => [
      componentFactory::Template('/pages/test-pages/redbean/'),
    ]
  ]
];
