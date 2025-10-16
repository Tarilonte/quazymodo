<?php

use Quazymodo\ComponentFactory;

return [
  'extends' => '/pages/base/',
  'inserts' => [
    'body' => [
      ComponentFactory::Plugin('/plugins/navbar/'),
      
    ],
    'navbar-logo' => ComponentFactory::Template(
      '/plugins/logo/',
      ['logo-class' => 'h-8 fill-primary']
    ),
  ]
];