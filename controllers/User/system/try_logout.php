<?php
/*
|-----------------------------------------------------------
| logout.php
|-----------------------------------------------------------
|
*/

require_once '../data/Model/User.php';
Model\User::logout();
header('Location: /');