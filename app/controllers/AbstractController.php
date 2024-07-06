<?php

namespace Controller;

use Psr\Http\Message\ResponseInterface;
use Laminas\Diactoros\Response;
use Quazymodo\Component;

abstract class AbstractController
{
  public function render(Component $component, int $status = 200): ResponseInterface
  {
    $response = new Response;
    $response->getBody()->write($component->render());
    $response = $response->withStatus($status);
    return $response;
  }
}