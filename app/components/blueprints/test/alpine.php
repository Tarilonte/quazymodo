<?php

use Quazymodo\ComponentFactory;
use Quazymodo\CSPManager;



return [
  'extends' => 'page-base',
  'js' => [
    "https://unpkg.com/alpinejs [defer]",
    ],
  'inserts' => [  
    'body' => [
      ComponentFactory::create(componentName:'navbar-01'),
      ComponentFactory::create(componentName:'alpine-test', componentType:'templateOnly'),
    ],
    'body-class' => 'flex flex-col',
    'navbar-start' => 'Daisy Test',
    'navbar-logo' => ComponentFactory::create(componentName:'logo', componentType:'templateOnly'),
    'logo-class' => 'h-8 fill-primary'   
  ] 
];