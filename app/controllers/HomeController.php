<?php

namespace Controller;

use Psr\Http\Message\ResponseInterface;
use Quazymodo\AbstractController;
use Quazymodo\ComponentFactory;

class HomeController extends AbstractController
{
  public function index(): ResponseInterface
  {
    $page = ComponentFactory::create("page-home");
    return $this->html($page);
  }
}