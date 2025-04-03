<?php

return [
  'extends' => 'page-base',
  'js' => [
    'https://unpkg.com/cally [type="module"]',
    'teste-cally.js',
    ],
  'inserts' => [
    [
      'slot' => 'body',
      'type' => 'component',
      'source' => 'navbar-01',
    ],    
    [
      'slot' => 'body',
      'type' => 'template',
      'source' => 'daisy-test',
      'content' => [
        "navbar-start" => "Daisy Test"
        ]
      ],    
      [
        'slot' => 'navbar-logo',
        'type' => 'template',
        'source' => 'logo',
        'content' => ["logo-class" => "h-8 fill-primary"]
      ]    
  ] 
];