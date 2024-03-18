<?php

// Inicia o sistema
require '../core/init.php';

// Instancia a classe AntiXSS
$antiXSS = new \voku\helper\AntiXSS();

// Processa a requisição
$match = $router->match(strtolower($_SERVER['REQUEST_URI']));
if (is_array($match)) {
    // Limpa os parâmetros GET e POST usando voku/anti-xss
    $_GET = $antiXSS->xss_clean($_GET);
    $_POST = $antiXSS->xss_clean($_POST);

    $controller = "../controllers/" . $match['target'] . ".php";
    if (!file_exists($controller)) {
        die("Controller [$controller] not found.");
    }
    require $controller;
} else {
    require "../controllers/_404.php";
}