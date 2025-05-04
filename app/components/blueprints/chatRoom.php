<?php

use Quazymodo\ComponentFactory;

return [
  'extends' => 'page-base',
  'js' => [
    'https://js.pusher.com/7.2/pusher.min.js',
    'chat.js',
  ],
  'inserts' => [
    'page-title' => 'Chat Público',
    'body' => [
      ComponentFactory::create(
        'chatRoom',
        [],
        'templateOnly'
      ),
    ],
    'body-class' => 'bg-base-100 min-h-screen',
  ]
];
