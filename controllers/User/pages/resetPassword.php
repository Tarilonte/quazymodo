<?php
require_once '../data/Model/User.php';

$token = array_key_first($_GET);

if (!$token || !$usuario = Model\User::getUserBy('password_reset_token', $token)) {
  $page = new pangaTemplater\Component(
    "page-base",
    [
      "page-title" => "Token inválido",    
      "body" => [
        new pangaTemplater\Component("page/invalid-reset-token",[],"simpleTemplate"),
      ]
    ]
  );
} else {
  $page = new pangaTemplater\Component(
    "page-base",
    [
      "page-title" => "Redefinição de Senha",    
      "body" => [
        new pangaTemplater\Component("form-resetPassword"),
        new pangaTemplater\Component("modal-01")
      ],
      "reset-token" => $token,
      "site-logo" => new pangaTemplater\Component("logo-01",[],"simpleTemplate")
    ]
  );
}

die($page->render()->html);