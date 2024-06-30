<?php
header('Content-Type: text/plain');
// Captura a linha de requisição
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
$protocol = $_SERVER['SERVER_PROTOCOL'];
$request_line = "$method $uri $protocol";

// Captura os cabeçalhos HTTP
$headers = getallheaders();
$headers_string = '';
foreach ($headers as $name => $value) {
    $headers_string .= "$name: $value\r\n";
}

// Captura o corpo da requisição
$body = file_get_contents('php://input');

// Combina tudo em uma string
$request_raw = "$request_line\r\n$headers_string\r\n$body";

// Imprime a requisição completa
echo $request_raw;