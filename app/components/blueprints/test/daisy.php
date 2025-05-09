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
      ComponentFactory::loadTemplate(componentName:'daisy-test'),
    ],
    'navbar-start' => 'Daisy Test',
    'navbar-logo' => ComponentFactory::loadTemplate(componentName:'logo'),
    'logo-class' => 'h-8 fill-primary'   
  ] 
];