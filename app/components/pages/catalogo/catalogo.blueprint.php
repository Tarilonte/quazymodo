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
      'description' => 'Três esferas de porcelana pintadas à mão, com presença delicada e acabamento impecável. As opções de pintura permitem personalizar a peça com elegância, do minimal ao mais precioso.',
      'href' => '#',
    ]
  ),
  ComponentFactory::Template(
    componentName: '/pages/catalogo/catalogoItem',
    inserts: [
      'image-src' => '/assets/pages/catalogo/images/abajur-cris-01.jpg',
      'title' => 'Abajur Cris',
      'badge' => 'Colecao CASACOR 2025',
      'description' => 'Dois gomos de porcelana pintados à mão, com proporções contemporâneas e acabamento preciso. Uma peça de presença marcante, que cria uma luz confortável e sofisticada.',
      'href' => '#',
    ]
  ),
  ComponentFactory::Template(
    componentName: '/pages/catalogo/catalogoItem',
    inserts: [
      'image-src' => '/assets/pages/catalogo/images/abajur-joana-01.jpg',
      'title' => 'Abajur Joana',
      'badge' => 'Collab com @jojo.farhi',
      'description' => 'Três gomos de porcelana pintados à mão, equilibrados sobre uma base de acrílico, formando uma silhueta leve e refinada. Um abajur versátil, feito para iluminar com suavidade e estilo.',
      'href' => '#',
    ]
  ),
  ComponentFactory::Template(
    componentName: '/pages/catalogo/catalogoItem',
    inserts: [
      'image-src' => '/assets/pages/catalogo/images/abajur-aline-01.jpg',
      'title' => 'Abajur Aline',
      'badge' => '',
      'description' => 'Quatro esferas de porcelana pintadas à mão, empilhadas com leveza sobre base de acrílico. As opções de pintura permitem um resultado delicado e elegante, perfeito para luz suave.',
      'href' => '#',
    ]
  ),
  ComponentFactory::Template(
    componentName: '/pages/catalogo/catalogoItem',
    inserts: [
      'image-src' => '/assets/pages/catalogo/images/arandela-luna-01.jpg',
      'title' => 'Arandela Luna',
      'badge' => '',
      'description' => 'Um contraste de volumes que traz modernidade sem perder a delicadeza. Pintada 100% à mão, cria um ponto de luz elegante e cheio de personalidade.',
      'href' => '#',
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
