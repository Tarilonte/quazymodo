<?php

return [
  'extends' => 'page-base',
  'css' => '',
  'inserts' => [
    [
      'slot' => 'page-title',
      'type' => 'env-var',
      'source' => 'APP_NAME',
    ],
    [
      'slot' => 'body',
      'type' => 'component',
      'source' => 'navbar-01'
    ],
    [
      'slot' => 'body',
      'type' => 'template',
      'source' => 'pages/home'
    ],
    /* [
      'slot' => 'body-class',
      'content' => 'bg-gradient-to-b from-base-100 to-base-300'
    ], */
    [
      'slot' => 'logo',
      'type' => 'component',
      'source' => 'animatedBackground',
    ],
    [
      'slot' => 'logo',
      'type' => 'template',
      'source' => 'logo',
      'content' => ["logo-class" => "w-24 sm:w-32 fill-primary m-auto"]
    ]
  ] 
];