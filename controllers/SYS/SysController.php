<?php

/**
 *-----------------------------------------------------------
 * SysController
 *-----------------------------------------------------------
 *
 * Controla a rota "/Sys/[action]?[args]"
 * As ações disponíveis estã elencadas no enum 'action'
 * 
 * TODO: IMPLEMENTAR CONTROLE DE ACESSO PARA ESSA ROTA.
 *
 */

// Tipos de ação permitidas para a rota
enum action: string 
{
  use pangaFunctions\EnumFromName;
  case ETL_CVM = 'endpoint/etl_cvm';  
  case INDEX = 'endpoint/index';  
}

// Define a ação
$action = $match['params']['action'] ?? 'index';

// Saneamento dos argumentos da requisição
use voku\helper\AntiXSS;
$antiXss = new AntiXSS();
if (isset($_GET)) { $_GET = $antiXss->xss_clean($_GET); }
if (isset($_POST)) { $_POST = $antiXss->xss_clean($_POST); }

// Carrega o controller da ação
$controller = action::tryFromName(strtoupper($action));
if (isset($controller)) {  
  $controller = $controller->value;
  require "../controllers/SYS/$controller.php";
} else {
  require "../controllers/_404.php";
}