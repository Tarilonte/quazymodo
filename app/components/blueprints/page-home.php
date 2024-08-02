<?php

return [
  'extends' => 'page-base',
  'css' => 'animatedBackground.css',
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
    /* [
      'data-slot' => 'body-class',
      'data-content' => 'bg-gradient-to-b from-base-100 to-base-300'
    ], */
    [
      'data-slot' => 'logo',
      'data-type' => 'component',
      'data-source' => 'animatedBackground',
    ],
    [
      'data-slot' => 'logo',
      'data-type' => 'template',
      'data-source' => 'logo',
      'data-content' => ["logo-class" => "w-24 sm:w-32 fill-primary m-auto"]
    ]
  ] 
];