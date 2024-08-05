<?php

namespace Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Quazymodo\AbstractController;
use Quazymodo\ComponentFactory;

class _404Controller extends AbstractController
{
  public function index(ServerRequestInterface $request): ResponseInterface
  {
    $REQUEST_URI = $request->getServerParams()['REQUEST_URI'];

    $page = ComponentFactory::create(
      "page-404",
      ["requested-uri" => $REQUEST_URI]
      );

    return $this->makeHttpResponse($page, 404);
  }
}