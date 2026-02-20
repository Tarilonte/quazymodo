<?php

use App\Components\ComponentShortcuts as CS;
use Quazymodo\ComponentFactory as CP;

/*
 * Product page blueprint for Arandela Nina.
 *
 * Intencao: criar uma pagina dedicada de produto e montar as opcoes de
 * pintura via plugin reutilizavel para reduzir duplicacao de markup.
 */
$ninaPaintOptions = [
  CS::produtoOpcaoCard(
    optionName: 'Pintura Lisa',
    optionTitle: 'Pintura Lisa',
    optionPriceDisplay: 'R$ 2.140,00',
    optionPriceNumber: 2140,
    optionImage: '/assets/pages/produto/arandela-nina/images/arandela-nina-pintura-lisa.jpg',
    optionAlt: 'Arandela Nina com pintura lisa',
    optionValue: 'pintura-lisa',
    optionDescription: 'A nossa arandela classica, queridinha. Cores lindas que compoem qualquer ambiente e combinam perfeitamente com papeis de parede.'
  ),
  CS::produtoOpcaoCard(
    optionName: 'Pintura Lisa com poas em relevo Ouro/Prata',
    optionTitle: 'Lisa com poas em relevo Ouro/Prata',
    optionPriceDisplay: 'R$ 2.640,00',
    optionPriceNumber: 2640,
    optionImage: '/assets/pages/produto/arandela-nina/images/arandela-nina-pintura-lisa-poas-relevo-ouro-prata.jpg',
    optionAlt: 'Arandela Nina com pintura lisa e poas em relevo',
    optionValue: 'pintura-lisa-poas-relevo',
    optionDescription: 'A versao classica com poas em relevo, finalizada com aplicacao artesanal de ouro e prata de verdade para um efeito sofisticado e exclusivo.'
  ),
  CS::produtoOpcaoCard(
    optionName: 'Pintura Listrada',
    optionTitle: 'Pintura Listrada',
    optionPriceDisplay: 'R$ 2.440,00',
    optionPriceNumber: 2440,
    optionImage: '/assets/pages/produto/arandela-nina/images/arandela-nina-pintura-listrada.jpg',
    optionAlt: 'Arandela Nina com pintura listrada',
    optionValue: 'pintura-listrada',
    optionDescription: 'Um visual leve e marcante, com contraste elegante para dar personalidade ao ambiente sem pesar.'
  ),
  CS::produtoOpcaoCard(
    optionName: 'Pintura Listrada com mini-poas em ouro',
    optionTitle: 'Listrada com mini-poas em ouro',
    optionPriceDisplay: 'R$ 2.590,00',
    optionPriceNumber: 2590,
    optionImage: '/assets/pages/produto/arandela-nina/images/arandela-nina-pintura-listrada-mini-poas-ouro.jpg',
    optionAlt: 'Arandela Nina com pintura listrada e mini-poas em ouro',
    optionValue: 'pintura-listrada-mini-poas',
    optionDescription: 'O ritmo das listras recebe mini-poas em ouro para destacar a peca com textura visual e brilho delicado.'
  ),
  CS::produtoOpcaoCard(
    optionName: 'Pintura Babadinho com friso em Ouro',
    optionTitle: 'Babadinho com friso em Ouro',
    optionPriceDisplay: 'R$ 3.050,00',
    optionPriceNumber: 3050,
    optionImage: '/assets/pages/produto/arandela-nina/images/arandela-nina-pintura-floral-artistica.jpg',
    optionAlt: 'Arandela Nina com pintura babadinho e friso em ouro',
    optionValue: 'pintura-babadinho',
    optionDescription: 'Delicada e charmosa, com acabamento artesanal que traz movimento e um toque romantico para ambientes especiais.'
  ),
  CS::produtoOpcaoCard(
    optionName: 'Pintura Floral/Artistica',
    optionTitle: 'Pintura Floral/Artistica',
    optionPriceDisplay: 'R$ 3.640,00',
    optionPriceNumber: 3640,
    optionImage: '/assets/pages/produto/arandela-nina/images/arandela-nina-pintura-babadinho-friso-ouro.jpg',
    optionAlt: 'Arandela Nina com pintura floral artistica',
    optionValue: 'pintura-floral',
    optionDescription: 'Estampa autoral com presenca artistica, ideal para quem quer uma peca decorativa como destaque principal do ambiente.'
  ),
];

return [
  'extends' => '/pages/base/',
  'inserts' => [
    'page-title' => 'Arandela Nina',
    'body-class' => 'h-dvh flex flex-col overflow-hidden',
    'body' => [
      CP::Plugin(componentName: '/plugins/navbar/navbar'),
      CP::Template(componentName: '/pages/produto/arandela-nina/'),
    ],
    'navbar-container-class' => ' shadow-lg',
    'navbar-class' => ' md:h-[80px]',
    'nina-paint-options' => $ninaPaintOptions,
  ],
];
