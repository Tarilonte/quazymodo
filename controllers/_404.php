<?php

// TODO: Criar págia 404

$URL = $_SERVER['REQUEST_URI'];
//echo "[404 controller] Não existe rota definida para: $URL";

$page = new pangaTemplater\Component(
  "page-base",
  [
    "page-title" => $_ENV["SITE_NAME"],
    "css" => "https://cdn.jsdelivr.net/npm/@tabler/icons-webfont/tabler-icons.min.css",
    "body-class" => "flex justify-center items-center p-10",
    "body" => new pangaTemplater\Component(
      "page/404",
      [
        "requested-url" => $URL
      ],
      "htmlOnly"
    )
  ]
  );
die($page->render()->html);