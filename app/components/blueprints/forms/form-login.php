<?php

use Quazymodo\ComponentFactory;

return [
  'extends' => 'page-base',
  'css' => [],
  'js' => [],
  'inserts' => [    
    'page-title' => 'Login',
    'body' => [
      ComponentFactory::create('navbar-01'),
      ComponentFactory::create(
        'forms/form-login', 
        ['body-class' => 'flex flex-col md:bg-base-200'],
        'templateOnly'
      ),
    ],
    'csrf-token' => $_SESSION['csrf-token'],
    'site-logo' => ComponentFactory::create(
      'logo', 
      ['logo-class' => 'h-16 fill-primary align-self-center'],
      'templateOnly'
    ),
  ]
];