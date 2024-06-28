<?php

$content = [
  "page-title" => $_ENV["SITE_NAME"],
  "body" => [
    new quazymodo\Component("navbar-01"),
    new quazymodo\Component("page-home")
  ],
];

$page = new quazymodo\Component("page-base", $content);

$page->render()->serve();