<?php
/*
|-----------------------------------------------------------
| processesLoginForm
|-----------------------------------------------------------
|
| Esse script é chamado quando o usuário efetua login com email e senha.
|
*/
sleep(3);
require_once '../data/Model/User.php';

// Valida o CSRF token
if (!is_csrf_valid()) die("Erro: Formulário inválido");

// Verifica as credenciais fornecidas
$usuario = Model\User::verifyCredentials(email:$_POST['email'], password:$_POST['password']);

// Credenciais inválidas
if (!$usuario) {
  die("00 - Credenciais inválidas");
}

// Redireciona para a página de boas-vindas
if ($usuario) {
  Model\User::login(usuario:$usuario);
  die("99 - Login efetuado com sucesso");
}