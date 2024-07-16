<?php

namespace Controller\ApPorcelanas;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Quazymodo\Component;
class HomeController extends \Controller\AbstractController
{
  public function index(ServerRequestInterface $request): ResponseInterface
  {
    $page = new Component(
      "ApPorcelanas/homepage",
    );
    return $this->render($page);
  }
}