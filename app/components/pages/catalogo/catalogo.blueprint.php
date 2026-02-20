<?php

use Quazymodo\ComponentFactory as CP;

/*
 * Catalogo page blueprint.
 *
 * Intencao: montar os itens do catalogo via templates reutilizaveis.
 */
$makeBadges = static function (string ...$badgeTexts): array {
  $badgeComponents = [];

  foreach ($badgeTexts as $badgeText) {
    $normalizedBadgeText = trim(string: $badgeText);

    if ($normalizedBadgeText === '') {
      continue;
    }

    $badgeComponents[] = CP::Template(
      componentName: '/pages/catalogo/produto-badge',
      inserts: [
        'badge-text' => $normalizedBadgeText,
      ]
    );
  }

  return $badgeComponents;
};

$catalogoItems = [
  CP::Template(
    componentName: '/pages/catalogo/catalogoItem',
    inserts: [
      'image-src' => '/assets/pages/catalogo/images/hero-1.jpg',
      'title' => 'Arandela Nina',
      'badges' => $makeBadges('Porcelana', 'Pintado à mão', 'Collab com @jojo.farhi'),
      'description' => 'Três esferas de porcelana com presença delicada e acabamento impecável. As opções de pintura permitem personalizar a peça com elegância, do minimal ao mais precioso.',
      'preco' => '2.140,00',
      'href' => '/produto/arandela-nina',
    ]
  ),
  CP::Template(
    componentName: '/pages/catalogo/catalogoItem',
    inserts: [
      'image-src' => '/assets/pages/catalogo/images/arandela-luna-01.jpg',
      'title' => 'Arandela Luna',
      'badges' => $makeBadges('Porcelana', 'Pintado à mão'),
      'description' => 'Um contraste de volumes que traz modernidade sem perder a delicadeza. Cria um ponto de luz elegante e cheio de personalidade.',
      'preco' => '1.940,00',
      'href' => '#',
    ]
  ),
  CP::Template(
    componentName: '/pages/catalogo/catalogoItem',
    inserts: [
      'image-src' => '/assets/pages/catalogo/images/abajur-cris-01.jpg',
      'title' => 'Abajur Cris',
      'badges' => $makeBadges('Porcelana', 'Pintado à mão', 'Colecao CASACOR 2025'),
      'description' => 'Dois gomos de porcelana com proporções contemporâneas e acabamento preciso. Uma peça de presença discreta, que cria uma luz confortável e sofisticada.',
      'preco' => '2.370,00',
      'href' => '#',
    ]
  ),
  CP::Template(
    componentName: '/pages/catalogo/catalogoItem',
    inserts: [
      'image-src' => '/assets/pages/catalogo/images/abajur-joana-01.jpg',
      'title' => 'Abajur Joana',
      'badges' => $makeBadges('Porcelana', 'Pintado à mão', 'Collab com @jojo.farhi'),
      'description' => 'Três gomos de porcelana sobre base de acrílico, formando uma silhueta leve e refinada. Um abajur versátil, feito para iluminar com suavidade e estilo.',
      'preco' => '2.370,00',
      'href' => '#',
    ]
  ),
  CP::Template(
    componentName: '/pages/catalogo/catalogoItem',
    inserts: [
      'image-src' => '/assets/pages/catalogo/images/abajur-aline-01.jpg',
      'title' => 'Abajur Aline',
      'badges' => $makeBadges('Porcelana', 'Pintado à mão'),
      'description' => 'Quatro esferas de porcelana, empilhadas com leveza sobre base de acrílico. As opções de pintura permitem um resultado delicado e elegante, perfeito para luz suave.',
      'preco' => '2.540,00',
      'href' => '#',
    ]
  ),
  CP::Template(
    componentName: '/pages/catalogo/catalogoItem',
    inserts: [
      'image-src' => '/assets/pages/catalogo/images/abajur-gomos-sofia-01.jpg',
      'title' => 'Abajur Sofia',
      'badges' => $makeBadges('Porcelana', 'Pintado à mão'),
      'description' => 'Um único gomo de porcelana que valoriza a beleza do essencial. A forma orgânica e o acabamento artesanal deixam a luz mais acolhedora e a decoração mais elegante.',
      'preco' => '2.140,00',
      'href' => '#',
    ]
  ),
  CP::Template(
    componentName: '/pages/catalogo/catalogoItem',
    inserts: [
      'image-src' => '/assets/pages/catalogo/images/abajur-gomos-ana-01.png',
      'title' => 'Abajur Ana',
      'badges' => $makeBadges('Porcelana', 'Pintado à mão'),
      'description' => 'Um abajur de piso imponente, com 14 gomos de porcelana e estrutura bem definida. Eleva o ambiente com elegância e um toque artesanal de alto nível.',
      'preco' => '7.420,00',
      'href' => '#',
    ]
  ),
  CP::Template(
    componentName: '/pages/catalogo/catalogoItem',
    inserts: [
      'image-src' => '/assets/pages/catalogo/images/pendente-balls-01.jpg',
      'title' => 'Pendente Balls',
      'badges' => $makeBadges('Porcelana', 'Pintado à mão'),
      'description' => 'Esferas de porcelana que criam um pendente leve, atual e marcante. Disponível em diferentes composições, para trazer personalidade com elegância.',
      'preco' => '2.550,00',
      'href' => '#',
    ]
  ),
  CP::Template(
    componentName: '/pages/catalogo/catalogoItem',
    inserts: [
      'image-src' => '/assets/pages/catalogo/images/cabideiros-01.jpg',
      'title' => 'Cabideiros',
      'badges' => $makeBadges('Porcelana', 'Pintado à mão'),
      'description' => 'Cabideiros de porcelana que transformam um detalhe funcional em peça de decoração. Versáteis e delicados, valorizam o ambiente com charme e acabamento refinado.',
      'preco' => '250,00',
      'href' => '#',
    ]
  ),
  CP::Template(
    componentName: '/pages/catalogo/catalogoItem',
    inserts: [
      'image-src' => '/assets/pages/catalogo/images/puxadores-01.jpg',
      'title' => 'Puxadores',
      'badges' => $makeBadges('Porcelana', 'Pintado à mão'),
      'description' => 'Puxadores de porcelana para elevar o acabamento de móveis com um toque artístico. Um detalhe pequeno que faz o conjunto parecer mais exclusivo.',
      'preco' => '150,00',
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
      CP::Plugin(componentName: '/plugins/navbar/navbar'),
      CP::Template(componentName: '/pages/catalogo/'),
    ],
    'catalogo-items' => $catalogoItems,
    'navbar-container-class' => ' shadow-lg',
    'navbar-class' => ' md:h-[80px]',
  ]
];
