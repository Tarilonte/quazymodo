<?php

require_once '../data/Model/User.php';

$token = array_key_first($_GET);

if (!$token) {
  die("Não foi possível validar seu email [01]");
}

// Busca o usuário pelo token
if ($usuario = Model\User::getUserBy('email_confirm_token', $token)) {
  $usuario = Model\User::confirmEmail($usuario['id']);
} else {
  die("Não foi possível validar seu email [02]");
}

$page = new pangaTemplater\Component(
  "page-base",
  [
    "page-title" => "mundofii ",
    "body" => new pangaTemplater\Component('drawer-01'),
    "page-content" => [
      new pangaTemplater\Component(
        'page/email-confirmado',
        ["usuario-nome" => $usuario['name']],
        'simpleTemplate'
      ),
    ],
  ]
);

die($page->render()->html);