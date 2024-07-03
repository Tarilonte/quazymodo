<?php

// Initialize quazymodo
require '../quazymodo/init.php';

// Match the request
$match = $router->match(strtolower($_SERVER['REQUEST_URI']));
if (is_array($match)) {
  // Clean the request
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