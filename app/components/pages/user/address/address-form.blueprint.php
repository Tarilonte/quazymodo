<?php

use Quazymodo\ComponentFactory;
use Quazymodo\Csrf;

return [
  'extends' => '/pages/base/base',
  'css' => 'form.css',
  'js' => [ASSET_HTMX],
  'inserts' => [    
    'page-title' => 'Endereço',
    'body' => [
      componentFactory::Template('/pages/user/address/address-form'),
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