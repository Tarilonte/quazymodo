<?php

use Quazymodo\ComponentFactory;

return [
  'extends' => 'page-base',
  'inserts' => [  
    'body' => [
      ComponentFactory::create(componentName:'navbar-01'),
      ComponentFactory::create(componentName:'toastComponent')
    ],
  ] 
];