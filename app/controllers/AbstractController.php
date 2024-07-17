<?php

namespace Controller;

use Psr\Http\Message\ResponseInterface;
use Nyholm\Psr7\Response;
use Quazymodo\Component;

abstract class AbstractController
{
  public function render(Component $component, int $status = 200): ResponseInterface
  {
    $response = new Response;
    $response->getBody()->write($component->render());
    $response = $response->withStatus($status);

    if ($component->getCspHeader()) {
      $response = $response->withAddedHeader('Content-Security-Policy', $component->getCspHeader());
    }
    return $response;
  }
}
