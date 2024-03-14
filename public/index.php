<?php

// Inicia o sistema
require '../core/init.php';

// Processa a requisição
$match = $router->match(strtolower($_SERVER['REQUEST_URI']));
if(is_array($match)){
  $controller = "../controllers/" . $match['target']. ".php";
  if (!file_exists($controller)) {
    die("Controller [$controller] not found.");
  }
  require $controller;
} else {
  require "../controllers/_404.php";
}