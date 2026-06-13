<?php

use App\Components\ComponentShortcuts as UI;
use Quazymodo\ComponentFactory;

/*
 * Examples page blueprint.
 *
 * Intencao: documentar componentes reutilizaveis com demos interativas.
 */
return [
  'extends' => '/pages/base/',
  'js' => ['exemplos.js'],
  'inserts' => [
    'page-title' => 'Exemplos de Componentes',
    'body-class' => 'bg-base-200/40',
    'body' => [
      ComponentFactory::Plugin(componentName: '/plugins/navbar/'),
      ComponentFactory::Plugin(componentName: '/plugins/modalComponent/'),
      ComponentFactory::Plugin(componentName: '/plugins/toastComponent/'),
      ComponentFactory::Template(
        componentName: '/pages/exemplos/',
        inserts: [
          // Isola cada exemplo em seu proprio template para manutencao mais simples.
          'modal-example-card' => ComponentFactory::Template(
            componentName: '/pages/exemplos/modal-example-card',
          ),
          'toast-example-card' => ComponentFactory::Template(
            componentName: '/pages/exemplos/toast-example-card',
          ),
          'vertical-table-example-card' => ComponentFactory::Template(
            componentName: '/pages/exemplos/vertical-table-example-card',
            inserts: [
              'vertical-table-demo' => UI::verticalTable(
                rows: [
                  'componente' => 'verticalTable',
                  'renderizacao' => 'Server-side (PHP blueprint)',
                  'uso_indicado' => 'Metadados e chave/valor',
                  'customizacao' => 'Classes por linha/cabecalho/celula',
                  'dependencias' => 'Tailwind + daisyUI',
                  'status' => 'ativo',
                ],
                options: [
                  'table-class' => 'table-zebra bg-base-100 border border-neutral overflow-hidden',
                  'th-class' => 'w-44 text-primary font-semibold',
                  'td-class' => 'text-base-content',
                ],
              ),
            ],
          ),
        ],
      ),
    ],
    'navbar-center' => '<span class="badge badge-outline">Dev</span>',
  ],
];
