<?php

if (isset($_SESSION['USER']['user-id'])) {
  header("Location: /");
  die();
}

$page = new pangaTemplater\Component(
  "page-base",
  [
    "page-title" => "Login",    
    "body" => [
      new pangaTemplater\Component("form-userLogin"),
      new pangaTemplater\Component("modal-01"),
    ],
    "site-logo" => new pangaTemplater\Component("logo-01",[],"simpleTemplate")
  ]
);

die($page->render()->html);