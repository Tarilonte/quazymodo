<?php
/*
|-----------------------------------------------------------
| enterWithGoogle
|-----------------------------------------------------------
|
| Esse script é chamado quando o usuário faz login ou registra-se com o Google.
| Referência: https://developers.google.com/identity/gsi/web/guides/verify-google-id-token
|
*/

require_once '../data/Model/User.php';

// Recebe o payload do Google com as informações do usuário
$client = new Google_Client(['client_id' => $_ENV['GOOGLE_OAUTH_CLIENT_ID']]);
$payload = $client->verifyIdToken($_POST['credential']);
if ($payload) {
  $email = $payload['email'];
  $nome = $payload['given_name'];
  $picture = $payload['picture'];
  $email_verified = $payload['email_verified'];
} else {
  die("Não foi possível entrar com o Google.");
}

// Verifica se já possui registro
$usuario = Model\User::getUserBy('email', $payload['email']);

// Se possui registro, então efetua o login
if ($usuario) {
  Model\User::login(usuario:$usuario);
} else {
  $usuario = Model\User::registerFromGoogle(googlePayload:$payload);
  (new pangaMailer\Mailer)->send_newUser_email($usuario);
  Model\User::login(usuario:$usuario);
}

$page = new pangaTemplater\Component(
  "page-base",
  ["js" => "enterWithGoogle.js"]
);

die($page->render()->html);
