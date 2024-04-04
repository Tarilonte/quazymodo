<?php

$URL = $_SERVER['REQUEST_URI'];

$page = new quazyTemplater\Component(
  "page-base",
  [
    "page-title" => $_ENV["SITE_NAME"],
    "css" => "https://cdn.jsdelivr.net/npm/@tabler/icons-webfont/tabler-icons.min.css",
    "body-class" => "flex justify-center items-center p-10",
    "body" => new quazyTemplater\Component(
      "page/404",
      [
        "requested-url" => $URL
      ],
      "htmlOnly"
    )
  ]
  );
die($page->render()->html);