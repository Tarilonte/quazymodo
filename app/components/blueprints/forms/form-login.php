<?php

use Quazymodo\ComponentFactory;
use Quazymodo\Csrf;

return [
  'extends' => 'page-base',
  'css' => ['form-login.css'],
  'js' => ['https://unpkg.com/htmx.org@2.0.4/dist/htmx.js'],
  'inserts' => [    
    'page-title' => 'Login',
    'body' => [
      ComponentFactory::create('modalComponent'),
      ComponentFactory::create(
        'themeSelector-01',
        ['btn-themeSelector-01-css' => 'absolute top-0 right-0 m-8'],
      ),
      ComponentFactory::loadTemplate('forms/form-login'),
    ],
    'body-class' => 'flex flex-col md:bg-base-200',
    'csrf-token' => Csrf::setToken(),
    'site-logo' => ComponentFactory::loadTemplate(
      'logo', 
      ['logo-class' => 'h-16 fill-primary align-self-center'],
    ),
  ]
];