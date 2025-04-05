<?php

return [
  'extends' => 'page-base',
  'js' => 'teste-modal.js',
  'inserts' => [
    [
      'slot' => 'page-title',
      'content' => 'Teste Modal'
    ],
    [
      'slot' => 'body',
      'type' => 'component',
      'source' => 'navbar-01',
      'content' => ["navbar-container-class" => "bg-base-100"]
    ],
    [
      'slot' => 'body',
      'type' => 'template',
      'source' => 'pages/modal_test-page'
    ],
    [
      'slot' => 'body-class',
      'content' => 'flex flex-col'
    ],
    [
      'slot' => 'body',
      'type' => 'component',
      'source' => 'modalComponent'
    ],
    [
      'slot' => 'navbar-logo',
      'type' => 'template',
      'source' => 'logo',
      'content' => ["logo-class" => "h-8 fill-primary"]
    ],
    [
      'slot' => 'navbar-start',
      'content' => 'Teste Modal'
    ]
  ]
];