<?php

namespace Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Quazymodo\Component;
class HomeController extends AbstractController
{
  public function index(ServerRequestInterface $request): ResponseInterface
  {    
    $query = $request->getQueryParams();
    $page = new Component(
      "page-home"
    );
    return $this->render($page);
  }
}