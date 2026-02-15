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
    'body' => [
      componentFactory::Plugin(
        componentName: '/plugins/theme-selector/',
        inserts: ['btn-themeSelector-01-css' => 'fixed top-0 right-0 m-8 z-50']
      ),
      componentFactory::Template(componentName: '/pages/home-sections/'),
    ],
  ]
];
