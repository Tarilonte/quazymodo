<?php

use Quazymodo\ComponentFactory;
use Quazymodo\CSPManager;



return [
  'extends' => 'page-base',
  'js' => [
    //"https://unpkg.com/alpinejs [defer]",
    ],
  'inserts' => [  
    'body' => [
      ComponentFactory::create(componentName:'navbar-01',shouldSetNonce:false),
      ComponentFactory::create(componentName:'alpine-test', componentType:'templateOnly',shouldSetNonce:false),
    ],
    'nonce' => CSPManager::getNonce(),
    'body-class' => 'flex flex-col',
    'navbar-start' => 'Daisy Test',
    'navbar-logo' => ComponentFactory::create(componentName:'logo', componentType:'templateOnly',shouldSetNonce:false),
    'logo-class' => 'h-8 fill-primary', 
  ] 
];