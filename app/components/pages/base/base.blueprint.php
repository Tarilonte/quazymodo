<?php

/*
 * Base page assets now load Tailwind and daisyUI from CDN.
 */
return [
  'template' => '/pages/base/',
  'css' => [
    ASSET_DAISYUI,
    'theme-silk.css',
    'theme-sunset.css',
    'base-cdn.css'
  ],
  'js' => [
    ASSET_JQUERY,
    ASSET_TAILWIND,
    'base.js',
    '/plugins/theme-selector/apply-theme.js'
  ]
];
