<?php

// Set the timezone and locale
date_default_timezone_set('America/Sao_Paulo');
setlocale(LC_ALL, 'pt_BR.utf8');
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');

// Start the session
session_start();

// Initialize quazymodo
require '../core/init.php';

// Initialize anti-xss
$antiXSS = new \voku\helper\AntiXSS();

// Match the request
$match = $router->match(strtolower($_SERVER['REQUEST_URI']));

if (is_array($match)) {
    // Limpa os parâmetros GET e POST usando voku/anti-xss
    $_GET = $antiXSS->xss_clean($_GET);
    $_POST = $antiXSS->xss_clean($_POST);

    $controller = "../app/controllers/" . $match['target'] . ".php";
    if (!file_exists($controller)) {
        die("Controller [".$match['target']."] not found.");
    }
    require $controller;
} else {
    require "../app/controllers/_404.php";
}