<?php

namespace Controller\Adminer;

class AdminerController
{
  public function index()
  {
    require __DIR__ . '/sqlite.php';
    die();
  }
}
