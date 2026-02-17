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
      'description' => 'Três esferas de porcelana com presença delicada e acabamento impecável. As opções de pintura permitem personalizar a peça com elegância, do minimal ao mais precioso.',
      'preco' => '2.140,00',
      'href' => '#',
    ]
  ),
  ComponentFactory::Template(
    componentName: '/pages/catalogo/catalogoItem',
    inserts: [
      'image-src' => '/assets/pages/catalogo/images/arandela-luna-01.jpg',
      'title' => 'Arandela Luna',
      'badge' => '',
      'description' => 'Um contraste de volumes que traz modernidade sem perder a delicadeza. Cria um ponto de luz elegante e cheio de personalidade.',
      'preco' => '1.940,00',
      'href' => '#',
    ]
  ),
  ComponentFactory::Template(
    componentName: '/pages/catalogo/catalogoItem',
    inserts: [
      'image-src' => '/assets/pages/catalogo/images/abajur-cris-01.jpg',
      'title' => 'Abajur Cris',
      'badge' => 'Colecao CASACOR 2025',
      'description' => 'Dois gomos de porcelana com proporções contemporâneas e acabamento preciso. Uma peça de presença discreta, que cria uma luz confortável e sofisticada.',
      'preco' => '2.370,00',
      'href' => '#',
    ]
  ),
  ComponentFactory::Template(
    componentName: '/pages/catalogo/catalogoItem',
    inserts: [
      'image-src' => '/assets/pages/catalogo/images/abajur-joana-01.jpg',
      'title' => 'Abajur Joana',
      'badge' => 'Collab com @jojo.farhi',
      'description' => 'Três gomos de porcelana sobre base de acrílico, formando uma silhueta leve e refinada. Um abajur versátil, feito para iluminar com suavidade e estilo.',
      'preco' => '2.370,00',
      'href' => '#',
    ]
  ),
  ComponentFactory::Template(
    componentName: '/pages/catalogo/catalogoItem',
    inserts: [
      'image-src' => '/assets/pages/catalogo/images/abajur-aline-01.jpg',
      'title' => 'Abajur Aline',
      'badge' => '',
      'description' => 'Quatro esferas de porcelana, empilhadas com leveza sobre base de acrílico. As opções de pintura permitem um resultado delicado e elegante, perfeito para luz suave.',
      'preco' => '2.540,00',
      'href' => '#',
    ]
  ),
  ComponentFactory::Template(
    componentName: '/pages/catalogo/catalogoItem',
    inserts: [
      'image-src' => '/assets/pages/catalogo/images/abajur-gomos-sofia-01.jpg',
      'title' => 'Abajur Sofia',
      'badge' => '',
      'description' => 'Um único gomo de porcelana que valoriza a beleza do essencial. A forma orgânica e o acabamento artesanal deixam a luz mais acolhedora e a decoração mais elegante.',
      'preco' => '2.140,00',
      'href' => '#',
    ]
  ),
  ComponentFactory::Template(
    componentName: '/pages/catalogo/catalogoItem',
    inserts: [
      'image-src' => '/assets/pages/catalogo/images/abajur-gomos-ana-01.jpg',
      'title' => 'Abajur Ana',
      'badge' => '',
      'description' => 'Um abajur de piso imponente, com 14 gomos de porcelana e estrutura bem definida. Eleva o ambiente com elegância e um toque artesanal de alto nível.',
      'preco' => '7.420,00',
      'href' => '#',
    ]
  ),
  ComponentFactory::Template(
    componentName: '/pages/catalogo/catalogoItem',
    inserts: [
      'image-src' => '/assets/pages/catalogo/images/pendente-balls-01.jpg',
      'title' => 'Pendente Balls',
      'badge' => '',
      'description' => 'Esferas de porcelana que criam um pendente leve, atual e marcante. Disponível em diferentes composições, para trazer personalidade com elegância.',
      'preco' => '2.550,00',
      'href' => '#',
    ]
  ),
  ComponentFactory::Template(
    componentName: '/pages/catalogo/catalogoItem',
    inserts: [
      'image-src' => '/assets/pages/catalogo/images/cabideiros-01.jpg',
      'title' => 'Cabideiros',
      'badge' => '',
      'description' => 'Cabideiros de porcelana que transformam um detalhe funcional em peça de decoração. Versáteis e delicados, valorizam o ambiente com charme e acabamento refinado.',
      'preco' => '250,00',
      'href' => '#',
    ]
  ),
  ComponentFactory::Template(
    componentName: '/pages/catalogo/catalogoItem',
    inserts: [
      'image-src' => '/assets/pages/catalogo/images/puxadores-01.jpg',
      'title' => 'Puxadores',
      'badge' => '',
      'description' => 'Puxadores de porcelana para elevar o acabamento de móveis com um toque artístico. Um detalhe pequeno que faz o conjunto parecer mais exclusivo.',
      'preco' => '150,00',
      'href' => '#',
    ]
  ),
];

return [
  'extends' => '/pages/base/',
  'js' => [
    //'catalogo.js',
  ],
  'inserts' => [
    'page-title' => 'Catálogo',
    'body' => [
      // componentFactory::Plugin(componentName: '/plugins/navbar/navbar'),
      componentFactory::Template(componentName: '/pages/catalogo/'),
    ],
    'catalogo-items' => $catalogoItems,
  ]
];
