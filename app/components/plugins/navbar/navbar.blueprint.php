<?php

use Quazymodo\ComponentFactory;

return [
  'template' => '/plugins/navbar/',
  'inserts' => [
    'navbar-end' => componentFactory::Plugin('/plugins/theme-selector/')
  ]
];
