<?php

use Quazymodo\ComponentFactory;

/*
 * Navbar plugin defaults.
 *
 * Intencao: expor um logo clicavel para a home sem exigir configuracao por pagina.
 */
return [
  'template' => '/plugins/navbar/',
  'inserts' => [
    'navbar-logo' => ComponentFactory::Template(
      componentName: '/plugins/logo/',
      inserts: [
        'logo-class' => 'w-8 fill-primary',
      ],
    ),
    'navbar-end' => componentFactory::Plugin('/plugins/theme-selector/'),
    'navbar-container-class' => 'w-full sticky top-0 z-30 bg-base-100',
    'navbar-class' => 'min-h-10 mx-auto max-w-screen-2xl px-2 md:px-8 md:py-4',
  ]
];
