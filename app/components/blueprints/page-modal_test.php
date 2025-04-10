<?php

use Quazymodo\ComponentFactory;

return [
  'extends' => 'page-base',
  'js' => 'teste-modal.js',
  'inserts' => [
    'page-title' => 'Teste Modal',
    'body' => [
      ComponentFactory::create(componentName:'navbar-01'),
      ComponentFactory::create(componentName:'pages/modal_test-page', componentType:'templateOnly'),
      ComponentFactory::create(componentName:'modalComponent'),
    ],
    'body-class'=> 'flex flex-col',
    'navbar-logo' => ComponentFactory::create(componentName:'logo', componentType:'templateOnly'),
    'logo-class' => 'h-8 fill-primary',
    'navbar-start' => 'Teste Modal'
  ]
];