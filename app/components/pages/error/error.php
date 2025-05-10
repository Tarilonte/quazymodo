<?php

use Quazymodo\ComponentFactory;

return [
  'extends' => '/pages/base/base',
  'css' => "https://cdn.jsdelivr.net/npm/@tabler/icons-webfont/tabler-icons.min.css",
  'inserts' => [
    'body' => componentFactory::Template('/pages/error/error'),
    'body-class' => 'flex justify-center items-center p-10',
  ]
];