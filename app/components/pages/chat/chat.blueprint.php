<?php

use Quazymodo\ComponentFactory;
use Quazymodo\Csrf;

return [
  'extends' => '/pages/base/',
  'css' => 'form-login.css',
  'js' => ASSET_HTMX,
  'inserts' => [    
    'page-title' => 'Entrar',
    'body' => [
      componentFactory::Plugin('/plugins/modalComponent/'),
      componentFactory::Plugin(
        '/plugins/theme-selector/',
        ['btn-themeSelector-01-css' => 'absolute top-0 right-0 m-8'],
      ),
      componentFactory::Template('/pages/chat/login'),
    ],
    'body-class' => 'flex flex-col md:bg-base-200',
    'csrf-token' => Csrf::setToken()
  ]
];