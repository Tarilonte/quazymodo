<?php

use Quazymodo\ComponentFactory;
use Quazymodo\Csrf;

return [
  'extends' => '/pages/base/',
  'css' => '/pages/user/login/form-login.css',
  'js' => [
    ASSET_JQUERY,
    ASSET_HTMX,
    'register-form.js'
  ],
  'inserts' => [    
    'page-title' => 'Registrar',
    'body' => [
      componentFactory::Template('/pages/user/register/'),
      componentFactory::Plugin('/plugins/modalComponent/'),
      componentFactory::Plugin('/plugins/theme-selector/',
        ['btn-themeSelector-01-css' => 'absolute top-0 right-0 m-8'],
      ),
    ],
    'body-class' => 'flex flex-col md:bg-base-200',
    'csrf-token' => Csrf::setToken(),
    'site-logo' => componentFactory::Template('/plugins/logo/', 
      ['logo-class' => 'h-16 fill-primary align-self-center'],
    ),
  ]
];
