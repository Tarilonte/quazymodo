<?php

use Quazymodo\ComponentFactory;
use App\Components\ComponentShortcuts;

return [
  'extends' => '/pages/test-pages/base-2/',
  'inserts' => [
    'body' => [
      ComponentShortcuts::verticalTable(
        [
          'nome' => 'Tarik Tarilonte',
          'idade' => 48
        ],
        ['th-class' => 'text-green-700']
      ),
    ]
  ]
];
