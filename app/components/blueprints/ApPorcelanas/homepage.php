<?php

return [
  'extends' => 'page-base',
  'inserts' => [
    [
      'slot' => 'page-title',
      'content' => 'Arte em Porcelana'
    ],
    [
      'slot' => 'body',
      'type' => 'component',
      'source' => 'navbar-01'
    ],
    [
      'slot' => 'navbar-class',
      'content' => 'h-28'
    ],
    [
      'slot' => 'navbar-center',
      'content' => '<h1 class="text-2xl font-bold text-base-500">Ana Paula Porcelanas</h1>'
    ],
    [
      'slot' => 'body-class',
      'content' => 'bg-gradient-to-l from-base-100 to-base-300'
    ],
    [
      'slot' => 'body',
      'type' => 'template',
      'source' => 'ApPorcelanas/homepage2'
    ]
  ]
];