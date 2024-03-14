<?php

if (isset($_SESSION['USER']['user-id'])) {
  header("Location: /");
  die();
}

$page = new pangaTemplater\Component(
  "page-base",
  [
    "page-title" => "Crie sua conta",    
    "site-logo" => new pangaTemplater\Component("logo-01",[],"simpleTemplate"),
    "body" => [
      new pangaTemplater\Component("form-userRegister"),
      new pangaTemplater\Component("modal-01")
    ]
  ]
);

die($page->render()->html); 