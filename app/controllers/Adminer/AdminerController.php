<?php

namespace Controller\Adminer;

class AdminerController
{
  public function index()
  {
    require __DIR__ . '/adminer.php';
    die();
  }
}
