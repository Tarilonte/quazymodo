<?php

use Quazymodo\ComponentFactory;

/*
 * Catalogo page blueprint.
 *
 * Intencao: montar os itens do catalogo via templates reutilizaveis.
 */
$catalogoItems = [
  ComponentFactory::Template(
    componentName: '/pages/catalogo/catalogoItem',
    inserts: [
      'image-src' => '/assets/pages/catalogo/images/hero-1.jpg',
      'title' => 'Arandela Nina',
      'badge' => 'Collab com @jojo.farhi',
      'description' => 'Tres delicadas bolas de porcelana pintadas a mao, que trazem um toque de sofisticacao a qualquer ambiente, essa e a Arandela Nina. Com opcoes de pintura variadas - desde cores lisas e listradas ate detalhes com poas de ouro - ela se adapta a diferentes estilos de decoracao.',
      'actions' => ComponentFactory::Template(
        componentName: '/pages/catalogo/buttonEncomendar',
        inserts: [
          'href' => '#',
        ]
      ),
    ]
  ),
  ComponentFactory::Template(
    componentName: '/pages/catalogo/catalogoItem',
    inserts: [
      'image-src' => '/assets/pages/catalogo/images/abajur-cris-01.jpg',
      'title' => 'Abajur Cris',
      'badge' => 'Colecao CASACOR 2025',
      'description' => 'O Abajur Gomos Cris, apresentado na CASACOR Rio 2025, e composto por dois gomos de porcelana pintados a mao sobre uma base de acrilico e uma elegante cupula cilindrica. Seu design equilibra forma e leveza, resultando em uma peca sofisticada e contemporanea. Ideal para ambientes que valorizam o trabalho artesanal e a iluminacao acolhedora.',
      'actions' => ComponentFactory::Template(
        componentName: '/pages/catalogo/buttonEncomendar',
        inserts: [
          'href' => '#',
        ]
      ),
    ]
  ),
  ComponentFactory::Template(
    componentName: '/pages/catalogo/catalogoItem',
    inserts: [
      'image-src' => '/assets/pages/catalogo/images/abajur-joana-01.jpg',
      'title' => 'Abajur Joana',
      'badge' => 'Collab com @jojo.farhi',
      'description' => 'O Abajur Gomos Joana e feito com tres gomos de porcelana pintados a mao e empilhados sobre uma base de acrilico. Disponivel nas opcoes Pintura Lisa, Listrada ou Vichy, combina com diferentes estilos de decoracao, trazendo elegancia e luz suave a ambientes como quartos, salas ou escritorios.',
      'actions' => ComponentFactory::Template(
        componentName: '/pages/catalogo/buttonEncomendar',
        inserts: [
          'href' => '#',
        ]
      ),
    ]
  ),
];

return [
  'extends' => '/pages/base/',
  'js' => [
    'catalogo.js',
  ],
  'inserts' => [
    'page-title' => 'Catálogo',
    'body-class' => 'h-dvh flex flex-col overflow-hidden',
    'body' => [
      // componentFactory::Plugin(componentName: '/plugins/navbar/navbar'),
      componentFactory::Template(componentName: '/pages/catalogo/'),
    ],
    'catalogo-items' => $catalogoItems,
  ]
];
