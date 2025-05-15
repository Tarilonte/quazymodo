<?php

use Quazymodo\ComponentFactory;

return [
  'extends' => '/pages/base/',
  'inserts' => [
    "body" => [
      componentFactory::Plugin("/plugins/navbar/"),
      $inserts["table"],
    ],
    "navbar-logo" =>  componentFactory::Template(
      "/plugins/logo/",
      ["logo-class" => "h-8 fill-primary"]
    ),
    "navbar-start" =>  "User Info",
  ]
];