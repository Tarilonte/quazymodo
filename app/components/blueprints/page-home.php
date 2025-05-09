<?php

use Quazymodo\ComponentFactory;

return [
  'extends' => 'page-base',
  'inserts' => [
    'page-title' => APP_NAME,
    'body' => [
      componentFactory::Component('navbar-01'),
      componentFactory::Template('pages/home'),
    ],
    'logo' => [
      componentFactory::Component('animatedBackground'),
      componentFactory::Template('logo'),
    ],
    'logo-class' => 'w-24 sm:w-32 fill-primary m-auto'
  ]
];