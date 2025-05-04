<?php

use Quazymodo\ComponentFactory;

return [
  'extends' => 'page-base',
  'js' => [
    'https://unpkg.com/centrifuge@5.3.4/dist/centrifuge.js',
    'centrifugo.js',
  ],
  'inserts' => [
    'page-title' => 'Centrifugo',
    'body' => '<div id="counter">-</div>',
    'body-class' => 'bg-base-100 min-h-screen',
  ]
];
