<?php

use Quazymodo\ComponentFactory;

return [
  'extends' => '/pages/base/base',
  'inserts' => [
    'body' => [
      ComponentFactory::Plugin('/plugins/navbar/'),
      'OLÁ FROM BASE-2 blueprint'  
    ]
  ]
];