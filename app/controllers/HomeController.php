<?php

namespace Controller;

use Psr\Http\Message\ResponseInterface;
use Quazymodo\AbstractController;
use Quazymodo\ComponentFactory;

class HomeController extends AbstractController
{
  public function index(): ResponseInterface
  {
    $page = componentFactory::Page("/pages/home/home");
    return $this->html($page);
  }
}