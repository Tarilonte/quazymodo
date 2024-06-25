<?php
date_default_timezone_set('America/Sao_Paulo');
setlocale(LC_ALL, 'pt_BR.utf8');
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
session_start();

/*
|--------------------------------------
| Carrega as classes do composer
|--------------------------------------
*/

// Carrega o autoload do composer
require '../vendor/autoload.php';

// Inicia o phpDotenv
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/core/..");
$dotenv->load();

// Inicia o AltoRouter e inclui as rotas
$router = new AltoRouter();
require '../core/routes.php';

/*
|--------------------------------------
| Carrega os arquivos do Core da aplicação
|--------------------------------------
*/
require '../core/quazyFunctions.php';
require '../core/session.php';
require '../core/quazyTemplater.php';