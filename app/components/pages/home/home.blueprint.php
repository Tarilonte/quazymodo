<?php

use Quazymodo\ComponentFactory;

return [
  'extends' => '/pages/base/',
  'inserts' => [
    'page-title' => APP_NAME,
    'body' => [
      componentFactory::Plugin('/plugins/navbar/'),
      componentFactory::Template('/pages/home/'),
    ],
    'logo' => [
      componentFactory::Plugin('/pages/home/animatedBackground'),
      componentFactory::Template('/plugins/logo/',['logo-class' => 'w-24 sm:w-32 fill-primary m-auto']),
    ]
  ]
];