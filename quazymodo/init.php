<?php 
  
  // Composer autoload
  require '../vendor/autoload.php';
  
  // Load App Environment Variables
  // (They are set in 'app/.env')
  $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../app");
  $dotenv->load();
  
  // Require App configuration
  require '../app/Config.php';
  
  // Initialize request object
  $request = Laminas\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
  );
  
  // Initialize PHPLeague Route and load App Routes
  $router = new League\Route\Router;
  require '../app/Routes.php';
  