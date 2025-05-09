<?php

use Quazymodo\ComponentFactory;

return [
  'extends' => 'page-base',
  'js' => 'teste-modal.js',
  'inserts' => [
    'page-title' => 'Teste Modal',
    'body' => [
      componentFactory::Component(componentName:'navbar-01'),
      componentFactory::Template(componentName:'pages/modal_test-page'),
      componentFactory::Component(componentName:'modalComponent'),
    ],
    'body-class'=> 'flex flex-col',
    'navbar-logo' => componentFactory::Template(componentName:'logo'),
    'logo-class' => 'h-8 fill-primary',
    'navbar-start' => 'Teste Modal'
  ]
];