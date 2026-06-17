<?php

use Quazymodo\Csrf;

/*
 * Environment selector plugin defaults.
 *
 * Intencao: expor a alternancia efemera do ambiente atual sem persistir
 * configuracao e sem depender de JavaScript no template.
 */
return [
  'template' => '/plugins/environmentSelector/',
  'js' => [
    ASSET_HTMX,
  ],
  'inserts' => [
    'csrf-token' => Csrf::setToken(),
    'production-checked' => APP_ENV === 'production' ? 'checked' : '',
    'environment-label' => APP_ENV === 'production' ? 'PROD' : 'DEV',
    'environment-badge-class' => APP_ENV === 'production' ? 'badge-warning' : 'badge-info',
  ],
];
