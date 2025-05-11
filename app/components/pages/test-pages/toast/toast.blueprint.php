<?php

use Quazymodo\ComponentFactory;

return [
  'extends' => '/pages/base/',
  'js' => 'test-makeToasts.js',
  'inserts' => [  
    'body' => [
      componentFactory::Plugin(componentName:'/plugins/navbar/'),
      componentFactory::Plugin(componentName:'/plugins/toastComponent/')
    ],
  ] 
];