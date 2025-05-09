<?php

namespace Controller;

use Psr\Http\Message\ResponseInterface;
use Quazymodo\AbstractController;
use Quazymodo\ComponentFactory;

class HomeController extends AbstractController
{
  public function index(): ResponseInterface
  {
    $page = componentFactory::Page("page-home");
    return $this->html($page);
  }
}