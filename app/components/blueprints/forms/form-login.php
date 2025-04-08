<?php
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
      'type' => 'component',
      'source' => 'navbar-01'
    ],
    [
      'slot' => 'body',
      'type' => 'template',
      'source' => 'forms/form-login',
      'content' => [ 'body-class' => 'flex flex-col md:bg-base-200' ]
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