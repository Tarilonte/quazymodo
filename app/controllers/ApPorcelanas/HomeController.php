<?php

namespace Controller\ApPorcelanas;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Quazymodo\AbstractController;
use Quazymodo\ComponentFactory;

class HomeController extends AbstractController
{
  public function index(ServerRequestInterface $request): ResponseInterface
  {
    $page = ComponentFactory::create(
      "ApPorcelanas/homepage",
    );
    return $this->render($page);
  }
}