<?php

use Quazymodo\ComponentFactory;

return [
  'extends' => 'page-base',
  'inserts' => [
    'page-title' => APP_NAME,
    'body' => [
      ComponentFactory::create('navbar-01'),
      ComponentFactory::create('pages/home',[],'templateOnly'),
    ],
    'logo' => [
      ComponentFactory::create('animatedBackground'),
      ComponentFactory::create('logo', [],'templateOnly'),
    ],
    'logo-class' => 'w-24 sm:w-32 fill-primary m-auto'
  ]
];