<?php

namespace Quazymodo;

use Psr\Http\Message\ResponseInterface;
use Nyholm\Psr7\Response;
use Quazymodo\BaseComponent;

abstract class AbstractController
{
  public function makeHttpResponse(BaseComponent $component, int $status = 200): ResponseInterface
  {
    $response = new Response;
    $response->getBody()->write($component->render());
    $response = $response->withStatus($status);

    if ($component->getCspHeader() !== null) {
      $response = $response->withAddedHeader('Content-Security-Policy', $component->getCspHeader());
    }

    return $response;
  }
}
