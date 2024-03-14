<?php

/*
|----------------------------------------------
| user-API.php
|----------------------------------------------
|
| Retorna os dados do usuário ou false se o usuário não for localizado
|
| Parâmetros:
| 'email'  => email do usuário, OU
| 'id'     => id do usuário,
| 'format' => [opcional] formato da saída: array(default) ou json
|
*/

show($_GET, '$_GET');

// Declara os parâmetros do recurso, disponíveis em cada método de requisição
switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    $parâmetros = ["email","id","format"];
    break;
  default:
    echo "Método " . $_SERVER['REQUEST_METHOD'] . " não disponível para esse recurso.";
    http_response_code(405); // Método Não Permitido
    die();
    break;
}


// Captura os argumentos da requisição
$email = isset($_GET['email']) ? filter_var($_GET['email'], FILTER_VALIDATE_EMAIL) : null;
$id = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : null;
$format = isset($_GET['format']) ? htmlspecialchars($_GET['format']) : null;


// Verifica se ambos ou nenhum foram fornecidos
if (empty($email) === empty($id)) {
    // Se ambos são vazios ou ambos são fornecidos, rejeita a requisição
    echo "Erro: É necessário informar exclusivamente 'email' ou 'id'.";
    // Para uma API, você pode querer enviar uma resposta HTTP apropriada também
    // http_response_code(400); // Bad Request
} else {
    // Processa a requisição

    // Se foi fornecido 'email'
    if (!empty($email)) {
        // Lógica para tratar a requisição com 'email'
        echo "Processando com email: $email";
    }
    
    // Se foi fornecido 'id'
    if (!empty($id)) {
        // Lógica para tratar a requisição com 'id'
        echo "Processando com id: $id";
    }
}





$usuarios = setDatabase('usuarios');
$usuario = $usuarios->get(
  'usuarios', // tabela
  "*" ,       // colunas
  ['email' => $_GET['email']] // clausula WHERE
);
show($usuario, '$usuario');
