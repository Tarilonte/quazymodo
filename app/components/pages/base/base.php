<?php

$daisyTheme = $_COOKIE['css-theme'] ?? '';

return [
  'template' => 'pages/base/base',
  'css' => ['base.css'],
  'js' => [
    ASSET_JQUERY,
    'base.js'
  ],
  'inserts' => [
    'css-theme' => $daisyTheme
  ]
];