<?php

use Quazymodo\ComponentFactory;

/*
 * Tema page blueprint.
 *
 * Intencao: montar uma pagina simples para visualizar tokens de cor do tema.
 */
return [
  'extends' => '/pages/base/',
  'inserts' => [
    'page-title' => 'Tema',
    'body' => [
      ComponentFactory::Plugin(componentName: '/plugins/navbar/'),
      ComponentFactory::Template(componentName: '/pages/tema/'),
    ],
  ],
];
