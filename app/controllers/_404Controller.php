<?php

namespace Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response;
use Quazymodo\Component;

class _404Controller
{
  public function index(ServerRequestInterface $request): ResponseInterface
  {
    $REQUEST_URI = $request->getServerParams()['REQUEST_URI'];

    $page = new Component(
      "page-404",
      ["requested-uri" => $REQUEST_URI]
      );

    $response = new Response;
    $response->getBody()->write($page->render());
    $response = $response->withStatus(404);
    return $response;
  }
}