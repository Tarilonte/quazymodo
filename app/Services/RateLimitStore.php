<?php

namespace App\Services;

final class RateLimitStore
{
  private const BEAN_TYPE = 'ratelimitentry';
  private const DB_ALIAS = 'rate_limit';
  private static bool $initialized = false;

  public function __construct(private ?string $sqlitePath = null)
  {
    $this->initialize();
  }

  public function hit(string $key, int $limit, int $period, ?int $now = null): array
  {
    $timestamp = $now ?? time();
    $windowStart = intdiv($timestamp, $period) * $period;
    $windowEnd = $windowStart + $period;
    $keyHash = hash('sha256', $key);

    return $this->withRateLimitDatabase(function () use ($keyHash, $windowStart, $windowEnd, $timestamp, $limit, $period) {
      $entry = RedBeanService::findOne(self::BEAN_TYPE, ' key_hash = ? ', [$keyHash]);

      if (!$entry || (int) ($entry->window_start ?? -1) !== $windowStart) {
        $entry = $entry ?: RedBeanService::dispense(self::BEAN_TYPE);
        $entry->key_hash = $keyHash;
        $entry->window_start = $windowStart;
        $entry->window_end = $windowEnd;
        $entry->period_seconds = $period;
        $entry->hits = 0;
      }

      $hits = (int) ($entry->hits ?? 0);

      if ($hits >= $limit) {
        $retryAfter = max(1, $windowEnd - $timestamp);
        return [
          'allowed' => false,
          'retry_after' => $retryAfter,
          'remaining' => 0,
          'reset_at' => $windowEnd,
        ];
      }

      $entry->hits = $hits + 1;
      $entry->updated_at = $timestamp;
      RedBeanService::store($entry);

      $this->cleanupExpiredWindows($timestamp);

      return [
        'allowed' => true,
        'retry_after' => 0,
        'remaining' => max(0, $limit - (int) $entry->hits),
        'reset_at' => $windowEnd,
      ];
    });
  }

  private function initialize(): void
  {
    if (self::$initialized) {
      return;
    }

    $path = $this->sqlitePath
      ?? (defined('RATE_LIMIT_DB_PATH')
        ? (string) constant('RATE_LIMIT_DB_PATH')
        : dirname(__DIR__) . '/writable/db/rate_limit.sqlite');
    $directory = dirname($path);

    if (!is_dir($directory)) {
      mkdir($directory, 0775, true);
    }

    RedBeanService::init();
    RedBeanService::registerSqliteDatabase(self::DB_ALIAS, $path);

    $this->withRateLimitDatabase(function () {
      RedBeanService::exec('CREATE TABLE IF NOT EXISTS ratelimitentry (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        key_hash TEXT NOT NULL,
        window_start INTEGER NOT NULL,
        window_end INTEGER NOT NULL,
        period_seconds INTEGER NOT NULL,
        hits INTEGER NOT NULL,
        updated_at INTEGER NOT NULL
      )');

      RedBeanService::exec('CREATE UNIQUE INDEX IF NOT EXISTS idx_rate_limit_key_hash ON ratelimitentry (key_hash)');
      RedBeanService::exec('CREATE INDEX IF NOT EXISTS idx_rate_limit_window_end ON ratelimitentry (window_end)');
    });

    self::$initialized = true;
  }

  private function withRateLimitDatabase(callable $callback): mixed
  {
    return RedBeanService::withDatabase(self::DB_ALIAS, $callback);
  }

  private function cleanupExpiredWindows(int $timestamp): void
  {
    if (random_int(1, 100) !== 1) {
      return;
    }

    RedBeanService::exec('DELETE FROM ratelimitentry WHERE window_end < ?', [$timestamp]);
  }
}
