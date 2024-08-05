<?php

namespace Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Quazymodo\AbstractController;
use Quazymodo\ComponentFactory;

class HomeController extends AbstractController
{
  public function index(ServerRequestInterface $request): ResponseInterface
  {
    $page = ComponentFactory::create(
      "page-home"
    );
    return $this->makeHttpResponse($page);
  }
}