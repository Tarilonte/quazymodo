<?php

use Quazymodo\ComponentFactory;
use function App\Components\verticalTable;

return [
  'extends' => '/pages/base/base-2',
  'inserts' => [
    'body' => [
      verticalTable(
        [
          'nome' => 'Tarik Tarilonte',
          'idade' => 48
        ],
        ['th-class' => 'text-green-700']
      ),
    ]
  ]
];