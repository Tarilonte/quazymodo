<?php

use Quazymodo\ComponentFactory;

return [
  'template' => '/plugins/appEnvironmentSelector/',
  'js' => [
    ASSET_HTMX,
  ],
  'inserts' => [
    'app-enviromnent' => APP_ENV,
    'aes-badge-class' => APP_ENV === 'production' ? 'badge-error' : 'badge-info',
  ]
];
