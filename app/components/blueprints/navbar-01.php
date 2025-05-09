<?php

use Quazymodo\ComponentFactory;

return [
  'template' => 'navbar-01',
  'inserts' => [
    'navbar-end' => componentFactory::Component('themeSelector-01')
  ]
];
