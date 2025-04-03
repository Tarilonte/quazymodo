<?php

return [
  'extends' => 'page-base',
  'css' => "https://cdn.jsdelivr.net/npm/@tabler/icons-webfont/tabler-icons.min.css",
  'inserts' => [
    [
      'slot' => 'body-class',
      'content' => 'flex justify-center items-center p-10'
    ]
  ]
];