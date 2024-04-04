<?php

$blueprint = [
  'type' => 'component',
  'template' => 'page/home',
  'css' => [],
  'js' => [],
  'data' => [
    [
      'data-slot' => 'logo',
      'data-type' => 'template',
      'data-source' => 'quasi-logo'
    ],
    [
      'data-slot' => 'quasi-logo-class',
      'data-type' => 'string',
      'data-source' => 'w-32 fill-primary m-auto'
    ]
  ]
];