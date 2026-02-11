<?php

use Quazymodo\ComponentFactory;
use App\Components\ComponentShortcuts as ui;

return [
  'extends' => '/pages/base/base-2',
  'inserts' => [
    'page-title' => 'Informações de ' . $inserts['userInfo']['name'],
    'body' => [
      ComponentFactory::Template('/pages/test-pages/user/'),      
    ],
    'table' => [
      ui::verticalTable(
        rows: $inserts['userInfo'],
        options: ['th-class' => 'text-blue-500']
      ),
      '<hr class="m-8">',
      ui::verticalTable(
        rows: $inserts['userInfo'],
        options: ['th-class' => 'text-green-700']
      ),
    ],
    'navbar-start' =>  'User Info',
  ]
];
