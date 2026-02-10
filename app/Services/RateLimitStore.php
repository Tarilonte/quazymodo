<?php

namespace App\Services;

final class RateLimitStore
{
  private const BEAN_TYPE = 'ratelimitentry';
  private const ABUSE_TABLE = 'ratelimitabuse';
  private const DB_ALIAS = 'rate_limit';
  private const MAX_SUSPENSION_SECONDS = 432000;
  private const SUSPENSION_STEPS = [300, 900, 3600, 21600, 86400, 172800, 259200, 432000];
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

  public function getActiveSuspension(string $ip, ?int $now = null): ?array
  {
    $normalizedIp = $this->normalizeIp($ip);

    if ($normalizedIp === null) {
      return null;
    }

    $timestamp = $now ?? time();

    return $this->withRateLimitDatabase(function () use ($normalizedIp, $timestamp) {
      $rows = RedBeanService::getAll(
        'SELECT strikes, suspended_until FROM ' . self::ABUSE_TABLE . ' WHERE ip = ? LIMIT 1',
        [$normalizedIp]
      );

      if (empty($rows)) {
        return null;
      }

      $row = $rows[0];
      $suspendedUntil = (int) ($row['suspended_until'] ?? 0);

      if ($suspendedUntil <= $timestamp) {
        return null;
      }

      return [
        'strikes' => (int) ($row['strikes'] ?? 0),
        'suspended_until' => $suspendedUntil,
        'retry_after' => max(1, $suspendedUntil - $timestamp),
      ];
    });
  }

  public function registerViolation(string $ip, ?int $now = null): ?array
  {
    $normalizedIp = $this->normalizeIp($ip);

    if ($normalizedIp === null) {
      return null;
    }

    $timestamp = $now ?? time();

    return $this->withRateLimitDatabase(function () use ($normalizedIp, $timestamp) {
      $rows = RedBeanService::getAll(
        'SELECT strikes, suspended_until FROM ' . self::ABUSE_TABLE . ' WHERE ip = ? LIMIT 1',
        [$normalizedIp]
      );

      $existing = $rows[0] ?? null;
      $currentStrikes = (int) ($existing['strikes'] ?? 0);
      $currentSuspendedUntil = (int) ($existing['suspended_until'] ?? 0);
      $newStrikes = $currentStrikes + 1;
      $suspensionSeconds = $this->suspensionSecondsForStrike($newStrikes);
      $baseTimestamp = max($timestamp, $currentSuspendedUntil);
      $newSuspendedUntil = $baseTimestamp + $suspensionSeconds;

      if ($existing === null) {
        RedBeanService::exec(
          'INSERT INTO ' . self::ABUSE_TABLE . ' (ip, strikes, suspended_until, last_violation_at, updated_at) VALUES (?, ?, ?, ?, ?)',
          [$normalizedIp, $newStrikes, $newSuspendedUntil, $timestamp, $timestamp]
        );
      } else {
        RedBeanService::exec(
          'UPDATE ' . self::ABUSE_TABLE . ' SET strikes = ?, suspended_until = ?, last_violation_at = ?, updated_at = ? WHERE ip = ?',
          [$newStrikes, $newSuspendedUntil, $timestamp, $timestamp, $normalizedIp]
        );
      }

      return [
        'strikes' => $newStrikes,
        'suspended_until' => $newSuspendedUntil,
        'retry_after' => max(1, $newSuspendedUntil - $timestamp),
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

      $abuseTableInfo = RedBeanService::getAll('PRAGMA table_info(' . self::ABUSE_TABLE . ')');
      if (!empty($abuseTableInfo)) {
        $ipColumn = array_values(array_filter(
          $abuseTableInfo,
          static fn(array $column): bool => ($column['name'] ?? '') === 'ip'
        ))[0] ?? null;

        $ipColumnType = strtoupper((string) ($ipColumn['type'] ?? ''));
        if ($ipColumnType !== 'TEXT') {
          RedBeanService::exec('DROP TABLE IF EXISTS ' . self::ABUSE_TABLE);
        }
      }

      RedBeanService::exec('CREATE TABLE IF NOT EXISTS ' . self::ABUSE_TABLE . ' (
        ip TEXT PRIMARY KEY,
        strikes INTEGER NOT NULL,
        suspended_until INTEGER NOT NULL,
        last_violation_at INTEGER NOT NULL,
        updated_at INTEGER NOT NULL
      )');
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

  private function normalizeIp(string $ip): ?string
  {
    $normalizedIp = trim($ip);

    if ($normalizedIp === '') {
      return null;
    }

    if (filter_var($normalizedIp, FILTER_VALIDATE_IP) === false) {
      return null;
    }

    return $normalizedIp;
  }

  private function suspensionSecondsForStrike(int $strike): int
  {
    $normalizedStrike = max(1, $strike);
    $index = min($normalizedStrike - 1, count(self::SUSPENSION_STEPS) - 1);
    return min(self::MAX_SUSPENSION_SECONDS, self::SUSPENSION_STEPS[$index]);
  }
}
