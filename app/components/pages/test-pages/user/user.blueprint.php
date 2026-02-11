<?php

use Quazymodo\ComponentFactory;
use App\Components\ComponentShortcuts;

return [
  'extends' => '/pages/base/base-2',
  'inserts' => [
    'page-title' => 'Informações de ' . $inserts['userInfo']['name'],
    'body' => [
      ComponentFactory::Template('/pages/test-pages/user/'),      
    ],
    'table' => [
      ComponentFactory::Plugin('/plugins/tableComponent/verticalTable/',['rows' => $inserts['userInfo'],'th-class' => 'text-blue-500']),
      '<hr class="m-8">',
      ComponentShortcuts::verticalTable($inserts['userInfo'], ['th-class' => 'text-green-700']),
    ],
    'navbar-start' =>  'User Info',
  ]
];
