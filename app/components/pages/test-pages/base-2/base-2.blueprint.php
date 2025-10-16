<?php

use Quazymodo\ComponentFactory;

return [
  'extends' => '/pages/base/base-2',
  'inserts' => [
    'body' => [
      ComponentFactory::Plugin('/plugins/navbar/'),   
    ]
  ]
];