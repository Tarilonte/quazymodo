<?php
// Carrega o arquivo com os termos de uso

  // Instancia o componente page-base e inclui o form-register
$page = new pangaTemplater\Component(
  "page-base",
  [
    "page-title" => "Termos de uso",
    "body" => new pangaTemplater\Component('drawer-01'),
    "page-content" => new pangaTemplater\Component('page/termos-de-uso',[],'simpleTemplate'),
  ]
  );

  die($page->render()->html); 