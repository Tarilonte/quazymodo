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
      ComponentFactory::loadTemplate(componentName:'alpine-test'),
    ],
    'nonce' => CSPManager::getNonce(),
    'body-class' => 'flex flex-col',
    'navbar-start' => 'Alpine JS Test',
    'navbar-logo' => ComponentFactory::loadTemplate(componentName:'logo'),
    'logo-class' => 'h-8 fill-primary', 
  ] 
];