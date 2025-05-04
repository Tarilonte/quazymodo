<?php

use Quazymodo\ComponentFactory;
use Quazymodo\CSPManager;



return [
  'extends' => 'page-base',
  'js' => [
    //"https://unpkg.com/alpinejs [defer]",
    'alpine-test.js'
    ],
  'inserts' => [  
    'body' => [
      ComponentFactory::create(componentName:'navbar-01'),
      ComponentFactory::create(componentName:'alpine-test', componentType:'templateOnly'),
    ],
    'nonce' => CSPManager::getNonce(),
    'body-class' => 'flex flex-col',
    'navbar-start' => 'Alpine JS Test',
    'navbar-logo' => ComponentFactory::create(componentName:'logo', componentType:'templateOnly'),
    'logo-class' => 'h-8 fill-primary', 
  ] 
];