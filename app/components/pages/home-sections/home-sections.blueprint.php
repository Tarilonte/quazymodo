<?php

use Quazymodo\ComponentFactory;

/*
 * Home sections page blueprint.
 *
 * Intencao: fornecer uma pagina alternativa com 5 secoes full-screen.
 */
return [
  'extends' => '/pages/base/',
  'js' => [
    'home-sections.js',
  ],
  'inserts' => [
    'page-title' => APP_NAME . ' - Full Sections',
    'body' => componentFactory::Template(componentName: '/pages/home-sections/'),
  ]
];
