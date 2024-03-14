<?php

/*
|------------------------
| Proteção CSRF
|------------------------
*/

// Cria o token CSRF
set_csrf();
// Função para setar o token CSRF
function set_csrf(): void {
	if (!isset($_SESSION["csrf-token"])){
		$_SESSION["csrf-token"] = bin2hex(random_bytes(16));
	}
}// Função para validar o token CSRF
function is_csrf_valid(): bool {
	if (!isset($_SESSION['csrf-token']) || !isset($_POST['csrf-token'])) {
		return false;
	}
	if ($_SESSION['csrf-token'] != $_POST['csrf-token']) {
		return false;
	}
	if ($_SESSION['csrf-token'] === $_POST['csrf-token']) {
		return true;
	}
	return false;	
}

/*
|------------------------
| Controle da sessão do usuário
|------------------------
*/

if (isset($_COOKIE['login_cookie']) &&!isset($_SESSION['USER'])) {
	require_once '../data/Model/User.php';
	$usuario = Model\User::getUserBy('login_cookie', $_COOKIE['login_cookie']);
	if ($usuario) {
		Model\User::login(usuario:$usuario);
	}
}

// Defina o limite de requisições por período (ex.: 5 requisições por minuto)
$limit = $_ENV['RATE_LIMIT_REQUESTS'];
$period = $_ENV['RATE_LIMIT_PERIOD'];

if (!isset($_SESSION['requests'])) {
    $_SESSION['requests'] = [];
}

// Limpa requisições antigas
$time = time();
$_SESSION['requests'] = array_filter($_SESSION['requests'], function ($timestamp) use ($time, $period) {
    return ($time - $timestamp) < $period;
});

if (count($_SESSION['requests']) >= $limit) {
    // Requisição bloqueada
    header('HTTP/1.1 429 Too Many Requests');
    echo "Você fez requisições demais. Tente novamente mais tarde.";
    exit;
}

// Registra a nova requisição
$_SESSION['requests'][] = $time;