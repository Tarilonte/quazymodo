<?php

namespace Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Quazymodo\Component;
class HomeController extends AbstractController
{
  public function index(ServerRequestInterface $request): ResponseInterface
  {
    $page = new Component(
      "page-home"
    );
    return $this->render($page);
  }
}