<?php

use Quazymodo\ComponentFactory;

return [
  'extends' => 'page-base',
  'css' => [],
  'js' => [],
  'inserts' => [
    [
      'slot' => 'page-title',
      'content' => 'Login',
    ],
    [
      'slot' => 'body',
      'content' => [
        ComponentFactory::create('navbar-01'),
        ComponentFactory::create('forms/form-login', ['body-class' => 'flex flex-col md:bg-base-200'],'templateOnly'),
        ]
    ],
    [
      'slot' => 'csrf-token',
      'type' => 'session-var',
      'source' => 'csrf-token'
    ],
    [
      'slot' => 'site-logo',
      'type' => 'template',
      'source' => 'logo',
      'content' => ['logo-class' => 'h-16 fill-primary align-self-center']
    ]
  ]
];