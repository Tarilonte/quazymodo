<?php

use Quazymodo\ComponentFactory;

return [
  'extends' => '/pages/base/',
  'css' => "https://cdn.jsdelivr.net/npm/@tabler/icons-webfont/tabler-icons.min.css",
  'inserts' => [
    'body' => componentFactory::Template('/pages/error/'),
    'body-class' => '',
  ]
];