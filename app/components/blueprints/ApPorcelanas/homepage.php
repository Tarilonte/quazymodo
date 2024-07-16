<?php

return [
  'extends' => 'page-base',
  'data' => [
    [
      'data-slot' => 'page-title',
      'data-content' => 'Arte em Porcelana'
    ],
    [
      'data-slot' => 'body',
      'data-type' => 'component',
      'data-source' => 'navbar-01'
    ],
    [
      'data-slot' => 'navbar-class',
      'data-content' => 'h-28'
    ],
    [
      'data-slot' => 'navbar-center',
      'data-content' => '<h1 class="text-2xl font-bold text-base-500">Ana Paula Porcelanas</h1>'
    ],
    [
      'data-slot' => 'body-class',
      'data-content' => 'bg-gradient-to-l from-base-100 to-base-300'
    ],
    [
      'data-slot' => 'body',
      'data-type' => 'template',
      'data-source' => 'ApPorcelanas/homepage2'
    ]
  ]
];