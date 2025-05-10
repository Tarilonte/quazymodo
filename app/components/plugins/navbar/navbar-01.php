<?php

use Quazymodo\ComponentFactory;

return [
  'template' => 'plugins/navbar/navbar-01',
  'inserts' => [
    'navbar-end' => componentFactory::Plugin('/plugins/theme-selector/themeSelector-01')
  ]
];
