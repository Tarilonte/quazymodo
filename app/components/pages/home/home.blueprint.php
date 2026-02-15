<?php

use Quazymodo\ComponentFactory;

/*
 * Home page blueprint.
 *
 * Intencao: manter a home original como pagina principal do site.
 */
return [
  'extends' => '/pages/base/',
  'inserts' => [
    'page-title' => APP_NAME,
    'body' => [
      componentFactory::Plugin(componentName: '/plugins/navbar/'),
      componentFactory::Template(componentName: '/pages/home/'),
    ],
    'logo' => [
      componentFactory::Plugin(
        componentName: '/plugins/animatedBackground/',
        inserts: ['bg-color' => 'bg-info/30']
      ),
      componentFactory::Template(
        componentName: '/plugins/logo/',
        inserts: ['logo-class' => 'w-24 sm:w-32 fill-primary m-auto']
      ),
    ],
  ]
];
