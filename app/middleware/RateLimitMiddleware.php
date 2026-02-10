<?php

namespace Middleware;

use App\Services\RateLimitStore;
use Controller\ErrorController;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Quazymodo\Helper;

class RateLimitMiddleware implements MiddlewareInterface
{
    private const APCU_KEY_PREFIX = 'quazymodo_rl_';
    private const APCU_SYNC_THRESHOLD = 0.8;
    private const APCU_TTL_GRACE = 5;
    private RateLimitStore $store;

    public function __construct(?RateLimitStore $store = null)
    {
      $this->store = $store ?? new RateLimitStore();
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
      [$limit, $period] = $this->resolvePolicy();

      if ($limit <= 0 || $period <= 0) {
        return $handler->handle($request);
      }

      $clientKey = Helper::getClientIp($request);
      $method = strtoupper($request->getMethod());
      $path = $request->getUri()->getPath();
      $rateKey = $method . '|' . $path . '|' . $clientKey;

      $activeSuspension = $this->store->getActiveSuspension($clientKey);

      if ($activeSuspension !== null) {
        $violation = $this->store->registerViolation($clientKey);
        $retryAfter = (int) ($violation['retry_after'] ?? $activeSuspension['retry_after'] ?? 1);
        return $this->rateLimitResponse($request, $retryAfter);
      }

      if ($this->isApcuFastPathEnabled()) {
        $fastPathHits = $this->incrementApcuWindowCounter($rateKey, $period);
        $syncThreshold = $this->syncThresholdHits($limit);

        if ($fastPathHits !== null && $fastPathHits < $syncThreshold) {
          return $handler->handle($request);
        }
      }

      $result = $this->store->hit($rateKey, $limit, $period);

      if (!$result['allowed']) {
        $violation = $this->store->registerViolation($clientKey);
        $retryAfter = (int) ($violation['retry_after'] ?? $result['retry_after'] ?? 1);
        return $this->rateLimitResponse($request, $retryAfter);
      }

      return $handler->handle($request);
    }

    private function resolvePolicy(): array
    {
      $defaultLimit = defined('RATE_LIMIT_REQUESTS') ? (int) RATE_LIMIT_REQUESTS : 60;
      $defaultPeriod = defined('RATE_LIMIT_PERIOD') ? (int) RATE_LIMIT_PERIOD : 60;

      return [$defaultLimit, $defaultPeriod];
    }

    private function rateLimitResponse(ServerRequestInterface $request, int $retryAfter): ResponseInterface
    {
      $retryAfter = max(1, $retryAfter);
      $controller = new ErrorController();
      $response = $controller->handle($request, 429);
      return $response->withHeader('Retry-After', (string) $retryAfter);
    }

    private function isApcuFastPathEnabled(): bool
    {
      if (!function_exists('apcu_enabled') || !function_exists('apcu_add') || !function_exists('apcu_inc')) {
        return false;
      }

      return \apcu_enabled();
    }

    private function incrementApcuWindowCounter(string $rateKey, int $period): ?int
    {
      $now = time();
      $windowStart = intdiv($now, $period) * $period;
      $ttlGrace = self::APCU_TTL_GRACE;
      $ttl = max(1, ($windowStart + $period + $ttlGrace) - $now);
      $cacheKey = self::APCU_KEY_PREFIX . hash('sha256', $rateKey . '|' . $windowStart);

      if (\apcu_add($cacheKey, 1, $ttl)) {
        return 1;
      }

      $success = false;
      $hits = \apcu_inc($cacheKey, 1, $success, $ttl);

      if ($success === false) {
        return null;
      }

      return (int) $hits;
    }

    private function syncThresholdHits(int $limit): int
    {
      $thresholdRatio = self::APCU_SYNC_THRESHOLD;
      $thresholdRatio = max(0.1, min(1.0, $thresholdRatio));
      return max(1, (int) floor($limit * $thresholdRatio));
    }
}
