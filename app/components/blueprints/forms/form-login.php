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
      componentFactory::Component('modalComponent'),
      componentFactory::Component(
        'themeSelector-01',
        ['btn-themeSelector-01-css' => 'absolute top-0 right-0 m-8'],
      ),
      componentFactory::Template('forms/form-login'),
    ],
    'body-class' => 'flex flex-col md:bg-base-200',
    'csrf-token' => Csrf::setToken(),
    'site-logo' => componentFactory::Template(
      'logo', 
      ['logo-class' => 'h-16 fill-primary align-self-center'],
    ),
  ]
];