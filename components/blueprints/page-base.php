<?php

$blueprint = [
  'type' => 'page',
  'template' => 'page-base',
  'css' => ['base.css'],
  'js' => [
    'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js',
    'base.js'
  ],
  'data' => [
    [
      'data-slot' => 'css-theme',
      'data-type' => 'cookie',
      'data-source' => 'css-theme'
    ]
  ]
];