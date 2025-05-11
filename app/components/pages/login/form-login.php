<?php

use Quazymodo\ComponentFactory;
use Quazymodo\Csrf;

return [
  'extends' => '/pages/base/base',
  'css' => 'form-login.css',
  'js' => ASSET_HTMX,
  'inserts' => [    
    'page-title' => 'Login',
    'body' => [
      componentFactory::Template('/pages/login/form-login'),
      componentFactory::Plugin('/plugins/modalComponent/modalComponent'),
      componentFactory::Plugin('/plugins/theme-selector/themeSelector-01',
        ['btn-themeSelector-01-css' => 'absolute top-0 right-0 m-8'],
      ),
    ],
    'body-class' => 'flex flex-col md:bg-base-200',
    'csrf-token' => Csrf::setToken(),
    'site-logo' => componentFactory::Template('/plugins/logo/logo', 
      ['logo-class' => 'h-16 fill-primary align-self-center'],
    ),
  ]
];