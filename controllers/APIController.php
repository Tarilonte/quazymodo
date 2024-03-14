<?php

/*
|-----------------------------------------------------------
| API Controller
|-----------------------------------------------------------
|
| Controla a rota "/API"
| Uma requisição API é formada pelas seguintes partes: /API/resource/?arguments, onde:
| "/API" direciona para o API controller
| "resource" especifica o recurso desejado (user, document, asset, etc.)
| "?arguments" são os argumentos da requisição (cada recurso possui seus próprios parâmetros)
|
*/

// TODO: Averiguar o verbo da requisição e direcionar para a API correspondente (separar APIs GET, POST em scripts diferentes)

// Saneamento dos argumentos da requisição
use voku\helper\AntiXSS;
$antiXss = new AntiXSS();
$resource = $antiXss->xss_clean($match['params']['resource']);
if (isset($_GET)) { $_GET = $antiXss->xss_clean($_GET); }
if (isset($_POST)) { $_POST = $antiXss->xss_clean($_POST); }

// Declara os recursos disponíveis
$available_resources = [
  "user"
];

// Verifica se o recurso solicitado está disponível e existe
if (in_array($resource, $available_resources) && file_exists("../data/API/$resource-API.php"))
{
  require "../data/API/$resource-API.php";
}else{
  header("HTTP/1.1 404 Not Found");
  echo "resource not available";
  die();
}