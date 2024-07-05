<?php

return [
  'extends' => 'page-base',
  'css' => "https://cdn.jsdelivr.net/npm/@tabler/icons-webfont/tabler-icons.min.css",
  'data' => [
    [
      'data-slot' => 'page-title',
      'data-type' => 'env_var',
      'data-source' => 'APP_NAME'
    ],
    [
      'data-slot' => 'body-class',
      'data-content' => 'flex justify-center items-center p-10 bg-accent'
    ],
    [
      'data-slot' => 'body',
      'data-type' => 'template',
      'data-source' => 'page/404'
    ]
  ]
];