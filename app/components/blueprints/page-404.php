<?php

return [
  'extends' => 'page-base',
  'css' => "https://cdn.jsdelivr.net/npm/@tabler/icons-webfont/tabler-icons.min.css",
  'inserts' => [
    [
      'slot' => 'page-title',
      'content' => '404 - Page Not Found'
    ],
    [
      'slot' => 'body-class',
      'content' => 'flex justify-center items-center p-10'
    ],
    [
      'slot' => 'body',
      'type' => 'template',
      'source' => 'pages/404'
    ]
  ]
];