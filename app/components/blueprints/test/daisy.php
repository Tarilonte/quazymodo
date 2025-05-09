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
      componentFactory::Component(componentName:'navbar-01'),
      componentFactory::Template(componentName:'daisy-test'),
    ],
    'navbar-start' => 'Daisy Test',
    'navbar-logo' => componentFactory::Template(componentName:'logo'),
    'logo-class' => 'h-8 fill-primary'   
  ] 
];