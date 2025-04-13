<?php

use Quazymodo\ComponentFactory;

return [
  'template' => 'navbar-01',
  'inserts' => [
    'navbar-end' => ComponentFactory::create('themeSelector-01')
  ]
];
