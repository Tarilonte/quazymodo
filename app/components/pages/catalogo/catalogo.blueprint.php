<?php

use Quazymodo\ComponentFactory;

/*
 * Catalogo page blueprint.
 *
 * Intencao: fornecer uma pagina alternativa com secoes full-screen.
 */
return [
  'extends' => '/pages/base/',
  'js' => [
    'catalogo.js',
  ],
  'inserts' => [
    'page-title' => APP_NAME . ' - Catalogo',
    'body-class' => 'h-dvh flex flex-col overflow-hidden',
    'body' => [
      // componentFactory::Plugin(componentName: '/plugins/navbar/navbar'),
      componentFactory::Template(componentName: '/pages/catalogo/'),
    ],
  ]
];
