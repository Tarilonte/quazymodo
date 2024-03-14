<?php
/*
|-----------------------------------------------------------
| try_resetPassword
|-----------------------------------------------------------
|
| Esse script é chamado quando o usuário tenta resetar a senha de acesso.
|
*/

require_once '../data/Model/User.php';

// Valida o CSRF token
if (!is_csrf_valid()) die("Erro: Formulário inválido");

if (!$usuario = Model\User::getUserBy('password_reset_token', $_POST['reset-token'])) {
  die("00: Token inválido");
}

if (Model\User::resetPassword($usuario['id'], $_POST['password1'])) {
  $mail = (new pangaMailer\Mailer)->send_redefinedPwd_msg($usuario);
  die("99: Senha alterada com sucesso");
}

die("00: Falha ao alterar a senha");
