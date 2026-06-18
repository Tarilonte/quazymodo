<?php

use Quazymodo\ComponentFactory;

return [
  'template' => '/plugins/appEnvironmentSelector/',
  'js' => [
    ASSET_HTMX,
  ],
  'inserts' => [
    'app-enviromnent' => APP_ENV,
    'aes-button-class' => APP_ENV === 'production' ? 'btn-error' : 'btn-info',
  ]
];
