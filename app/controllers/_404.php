<?php

use Quazymodo\Component;

$URL = $_SERVER['REQUEST_URI'];

$page = new Component(
  "page-base",
  [
    "page-title" => $_ENV["SITE_NAME"],
    "css" => "https://cdn.jsdelivr.net/npm/@tabler/icons-webfont/tabler-icons.min.css",
    "body-class" => "flex justify-center items-center p-10",
    "body" => new Component(
      "page/404",
      [
        "requested-url" => $URL
      ],
      "htmlOnly"
    )
  ]
  );
die($page->render()->html);