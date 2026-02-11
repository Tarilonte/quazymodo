<?php

use App\Components\ComponentShortcuts as ui;

return [
  'extends' => '/pages/test-pages/base-2/',
  'inserts' => [
    'body' => [
      ui::verticalTable(
        rows: [
          'nome' => 'Tarik Tarilonte',
          'idade' => 48
        ],
        options: ['th-class' => 'text-green-700']
      ),
    ]
  ]
];
