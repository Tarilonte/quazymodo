<?php

use Quazymodo\ComponentFactory;

return [
  'extends' => 'pages/base/base',
  'inserts' => [
    'page-title' => APP_NAME,
    'body' => [
      componentFactory::Plugin('/plugins/navbar/navbar-01'),
      componentFactory::Template('/pages/home/home'),
    ],
    'logo' => [
      componentFactory::Plugin('/pages/home/animatedBackground'),
      componentFactory::Template('/plugins/logo/logo',['logo-class' => 'w-24 sm:w-32 fill-primary m-auto']),
    ]
  ]
];