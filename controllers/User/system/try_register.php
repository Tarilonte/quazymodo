<?php
/*
|-----------------------------------------------------------
| try_register
|-----------------------------------------------------------
|
| Esse script é chamado quando o usuário registra-se com seu email e senha.
|
*/
sleep(1);
require_once '../data/Model/User.php';

// Valida o CSRF token
if (!is_csrf_valid()) die("Erro: Formulário inválido");

// Se já possui registro, interrompe o processo
if ($usuario = Model\User::getUserBy('email', $_POST['email'])) {
  die("00 - Usuário já cadastrado");
}
// Senão, registra o usuário
$usuario = Model\User::registerFromForm(form:$_POST);
if ($usuario) {
  Model\User::login(usuario:$usuario);
  (new pangaMailer\Mailer)->send_newUser_email($usuario);
  die("99 - Usuário cadastrado com sucesso");
} 

die("01 - Falha ao cadastrar usuário");