<?php

use Quazymodo\ComponentFactory;

return [
  'extends' => '/pages/base/base',
  'js' => 'test-makeToasts.js',
  'inserts' => [  
    'body' => [
      componentFactory::Plugin(componentName:'/plugins/navbar/navbar-01'),
      componentFactory::Plugin(componentName:'/plugins/toastComponent/toastComponent')
    ],
  ] 
];