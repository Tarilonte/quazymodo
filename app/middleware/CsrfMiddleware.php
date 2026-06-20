<?php

namespace Middleware;

use Controller\ErrorController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Quazymodo\Csrf;

/*
 * Centralized CSRF guard for browser-facing mutating routes.
 */
class CsrfMiddleware implements MiddlewareInterface
{
  private const string BODY_FIELD = 'csrf-token';
  private const string HEADER_NAME = 'X-CSRF-Token';

  public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
  {
    $token = $this->resolveToken(request: $request);

    if ($token === null || !Csrf::verifyToken(token: $token)) {
      // Keep failure path consistent with project error pages.
      return (new ErrorController())->handle(
        request: $request,
        statusCode: 403,
      );
    }

    return $handler->handle(request: $request);
  }

  private function resolveToken(ServerRequestInterface $request): ?string
  {
    $parsedBody = $request->getParsedBody();

    if (is_array($parsedBody)) {
      $bodyToken = $parsedBody[self::BODY_FIELD] ?? null;

      if (is_string($bodyToken) && trim($bodyToken) !== '') {
        return trim($bodyToken);
      }
    }

    $headerToken = trim($request->getHeaderLine(self::HEADER_NAME));

    if ($headerToken !== '') {
      return $headerToken;
    }

    return null;
  }
}
