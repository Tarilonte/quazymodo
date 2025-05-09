<?php

use Quazymodo\ComponentFactory;

return [
  'extends' => 'page-base',
  'inserts' => [
    'page-title' => APP_NAME,
    'body' => [
      ComponentFactory::create('navbar-01'),
      ComponentFactory::loadTemplate('pages/home'),
    ],
    'logo' => [
      ComponentFactory::create('animatedBackground'),
      ComponentFactory::loadTemplate('logo'),
    ],
    'logo-class' => 'w-24 sm:w-32 fill-primary m-auto'
  ]
];