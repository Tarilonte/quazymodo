<?php

namespace Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response;
use Quazymodo\Component;

class HomeController
{
  public function index(ServerRequestInterface $request): ResponseInterface
  {    
    $page = new Component(
      "page-home"
    );
    $response = new Response;
    $response->getBody()->write($page->render());
    return $response;
  }
}