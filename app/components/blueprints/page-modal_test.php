<?php

use Quazymodo\ComponentFactory;

return [
  'extends' => 'page-base',
  'js' => 'teste-modal.js',
  'inserts' => [
    'page-title' => 'Teste Modal',
    'body' => [
      ComponentFactory::create(componentName:'navbar-01'),
      ComponentFactory::loadTemplate(componentName:'pages/modal_test-page'),
      ComponentFactory::create(componentName:'modalComponent'),
    ],
    'body-class'=> 'flex flex-col',
    'navbar-logo' => ComponentFactory::loadTemplate(componentName:'logo'),
    'logo-class' => 'h-8 fill-primary',
    'navbar-start' => 'Teste Modal'
  ]
];