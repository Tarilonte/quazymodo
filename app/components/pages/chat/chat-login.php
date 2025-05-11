<?php

use Quazymodo\ComponentFactory;
use Quazymodo\Csrf;

return [
  'extends' => '/pages/base/base',
  'css' => ['form-login.css'],
  'js' => ['https://unpkg.com/htmx.org@2.0.4/dist/htmx.js'],
  'inserts' => [    
    'page-title' => 'Entrar',
    'body' => [
      componentFactory::Plugin('/plugins/modalComponent/modalComponent'),
      componentFactory::Plugin(
        '/plugins/theme-selector/themeSelector-01',
        ['btn-themeSelector-01-css' => 'absolute top-0 right-0 m-8'],
      ),
      componentFactory::Template('/pages/chat/chat-login'),
    ],
    'body-class' => 'flex flex-col md:bg-base-200',
    'csrf-token' => Csrf::setToken()
  ]
];