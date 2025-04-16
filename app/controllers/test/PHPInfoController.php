<?php

namespace Controller\Test;

use Psr\Http\Message\ResponseInterface;
use Quazymodo\AbstractController;
use Quazymodo\ComponentFactory;

class PHPInfoController extends AbstractController
{
  public function index(): ResponseInterface
  {
    phpinfo();
    die();
  }
}