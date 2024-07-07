<?php

return [
  'extends' => 'page-base',
  'js' => 'teste-modal.js',
  'data' => [
    [
      'data-slot' => 'page-title',
      'data-content' => 'Teste Modal'
    ],
    [
      'data-slot' => 'body',
      'data-type' => 'component',
      'data-source' => 'navbar-01',
      'data-content' => ["navbar-container-class" => "bg-base-100"]
    ],
    [
      'data-slot' => 'body',
      'data-type' => 'template',
      'data-source' => 'page/modal_test-page'
    ],
    [
      'data-slot' => 'body',
      'data-type' => 'component',
      'data-source' => 'modal-01'
    ],
    [
      'data-slot' => 'navbar-logo',
      'data-type' => 'template',
      'data-source' => 'logo',
      'data-content' => ["logo-class" => "h-8 fill-primary"]
    ],
    [
      'data-slot' => 'navbar-start',
      'data-content' => 'Teste Modal'
    ]
  ]
];