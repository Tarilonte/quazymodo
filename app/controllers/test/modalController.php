<?php

namespace Controller\Test;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response;
use Quazymodo\Component;

class modalController
{
  public function index(ServerRequestInterface $request): ResponseInterface
  {    
    $page = new Component("page-modal_test");
    $response = new Response;
    $response->getBody()->write($page->render());
    return $response;
  }
}