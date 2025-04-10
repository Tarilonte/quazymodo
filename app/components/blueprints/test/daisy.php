<?php

use Quazymodo\ComponentFactory;

return [
  'extends' => 'page-base',
  'js' => [
    "https://unpkg.com/cally [type='module']",
    'teste-cally.js',
    ],
  'inserts' => [  
    'body' => [
      ComponentFactory::create(componentName:'navbar-01'),
      ComponentFactory::create(componentName:'daisy-test', componentType:'templateOnly'),
    ],
    'navbar-start' => 'Daisy Test',
    'navbar-logo' => ComponentFactory::create(componentName:'logo', componentType:'templateOnly'),
    'logo-class' => 'h-8 fill-primary'   
  ] 
];