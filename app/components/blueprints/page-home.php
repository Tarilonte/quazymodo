<?php

return [
  'extends' => 'page-base',
  'data' => [
    [
      'data-slot' => 'page-title',
      'data-type' => 'env-var',
      'data-source' => 'APP_NAME',
    ],
    [
      'data-slot' => 'body',
      'data-type' => 'component',
      'data-source' => 'navbar-01'
    ],
    [
      'data-slot' => 'body',
      'data-type' => 'template',
      'data-source' => 'pages/home'
    ],
    [
      'data-slot' => 'logo',
      'data-type' => 'template',
      'data-source' => 'logo',
      'data-content' => ["logo-class" => "w-32 fill-primary m-auto"]
    ]
  ]
];