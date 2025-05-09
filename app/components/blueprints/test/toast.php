<?php

use Quazymodo\ComponentFactory;

return [
  'extends' => 'page-base',
  'inserts' => [  
    'body' => [
      componentFactory::Component(componentName:'navbar-01'),
      componentFactory::Component(componentName:'toastComponent')
    ],
  ] 
];