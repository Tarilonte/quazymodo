<?php

namespace Controller;

use Psr\Http\Message\ResponseInterface;
use Quazymodo\AbstractController;

class PHPInfoController
{
  public function index()
  {
    phpinfo();
    die();
  }
}