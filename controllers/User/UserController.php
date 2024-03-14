<?php

/**
 *-----------------------------------------------------------
 * UserController
 *-----------------------------------------------------------
 *
 * Controla a rota "/User/[action]" *
 * As ações dispníveis estã elencadas no enum 'action'
 *
 */

use pangaFunctions\EnumFromName;

// Tipos de ação permitidas para a rota
enum action: string 
{
  use EnumFromName;
  case LOGIN = 'pages/LoginForm';
  case PROCESSLOGINFORM ='system/try_login';
  
  case REGISTER = 'pages/RegisterForm';
  case PROCESSREGISTERFORM ='system/try_Register';
  case EMAILCONFIRM ='pages/EmailConfirm';
  
  case RESETPASSWORD = 'pages/resetPassword';
  case PROCESSRESETPASSWORD = 'system/try_resetPassword';
  
  case LOGOUT = 'system/try_Logout';
  case ENTERWITHGOOGLE = 'system/enterWithGoogle';
  case GENERATEPWDRESETTOKEN = 'system/generatePwdResetToken';
}

// Saneamento dos argumentos da requisição
use voku\helper\AntiXSS;
$antiXss = new AntiXSS();
$action = $antiXss->xss_clean($match['params']['action']);
if (isset($_GET)) { $_GET = $antiXss->xss_clean($_GET); }
if (isset($_POST)) { $_POST = $antiXss->xss_clean($_POST); }

// Carrega o controller da ação
$controller = action::tryFromName(strtoupper($action));
if (isset($controller)) {  
  $controller = $controller->value;
  require "../controllers/User/$controller.php";
} else {
  require "../controllers/_404.php";
}