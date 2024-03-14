<?php
/*
|-----------------------------------------------------------
| generatePwdResetToken
|-----------------------------------------------------------
|
| Esse script é chamado quando o usuário solicita a redefinição de senha
| a partir do formulário de login
|
*/
$password_reset_token = false;
require_once '../data/Model/User.php';

if (!is_csrf_valid()) die("Erro: Formulário inválido");

// Verifica se o usuário existe no banco de dados
if ($usuario = Model\User::getUserBy('email', $_POST['email'])) {
  $password_reset_token = Model\User::set_token($usuario['id'], 'password_reset_token');
}

if ($password_reset_token) {
  (new pangaMailer\Mailer)->send_resetPwd_token($usuario, $password_reset_token);
}

die("98 - Procedimento concluído com sucesso");