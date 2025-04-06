<?php

namespace Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class RateLimitMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
      $clientIp = \Quazymodo\Functions\getClientIp($request);

      $limit = $_ENV['RATE_LIMIT_REQUESTS'];
      $period = $_ENV['RATE_LIMIT_PERIOD'];

      if (!isset($_SESSION['rate_limit'])) {
        $_SESSION['rate_limit'] = [];
      }

      if (!isset($_SESSION['rate_limit'][$clientIp])) {
        $_SESSION['rate_limit'][$clientIp] = [];
      }

      $time = time();
      $_SESSION['rate_limit'][$clientIp] = array_filter($_SESSION['rate_limit'][$clientIp], function ($timestamp) use ($time, $period) {
        return ($time - $timestamp) < $period;
      });

      if (count($_SESSION['rate_limit'][$clientIp]) >= $limit) {
        throw new \Exception("", 429);
      }

      $_SESSION['rate_limit'][$clientIp][] = $time;

      return $handler->handle($request);
    }
}
