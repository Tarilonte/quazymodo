<?php

namespace App\Services;

use RedBeanPHP\R as R;
use Throwable;
use Tracy\Debugger;
use Tracy\IBarPanel;

/*
 * RedBeanService centraliza acesso ao RedBean com aliases e, em desenvolvimento,
 * coleta métricas de tempo para exibir um painel na Tracy Bar.
 */
final class RedBeanService
{
  private static array $registeredAliases = [];
  private static ?string $currentAlias = null;
  private static ?RedBeanProxy $proxy = null;
  private static bool $panelAdded = false;
  private static array $metrics = [
    'totalTime' => 0.0,
    'totalCalls' => 0,
    'operations' => [],
  ];

  public static function init(?string $sqlitePath = null, string $alias = 'default'): void
  {
    $path = $sqlitePath ?? self::defaultSqlitePath();
    self::registerSqliteDatabase($alias, $path);
  }

  public static function registerSqliteDatabase(string $alias, string $sqlitePath): void
  {
    if (isset(self::$registeredAliases[$alias])) {
      return;
    }

    R::addDatabase($alias, 'sqlite:' . $sqlitePath);
    self::$registeredAliases[$alias] = true;

    if (self::$currentAlias === null) {
      self::$currentAlias = $alias;
      R::selectDatabase($alias);
    }

    self::withDatabase($alias, function () {
      self::applySqlitePragmas();
    });

    if (defined('APP_ENV') && APP_ENV === 'production') {
      R::freeze(true);
    }

    self::ensureDebugPanel();
  }

  public static function withDatabase(string $alias, callable $callback): mixed
  {
    self::ensureAliasRegistered($alias);

    return self::measure('withDatabase', function () use ($alias, $callback) {
      $previousAlias = self::$currentAlias;

      if ($previousAlias !== $alias) {
        R::selectDatabase($alias);
        self::$currentAlias = $alias;
      }

      try {
        return $callback();
      } finally {
        if ($previousAlias !== null && $previousAlias !== $alias) {
          R::selectDatabase($previousAlias);
          self::$currentAlias = $previousAlias;
        }
      }
    });
  }

  private static function applySqlitePragmas(): void
  {
    R::exec('PRAGMA journal_mode = WAL');
    R::exec('PRAGMA synchronous = NORMAL');
    R::exec('PRAGMA temp_store = MEMORY');
    R::exec('PRAGMA foreign_keys = ON');
    R::exec('PRAGMA cache_size = -20000');
    R::exec('PRAGMA busy_timeout = 5000');
  }

  private static function ensureAliasRegistered(string $alias): void
  {
    if (isset(self::$registeredAliases[$alias])) {
      return;
    }

    if ($alias === 'default') {
      self::init();
      return;
    }

    throw new \RuntimeException("Alias de banco nao registrado: [$alias]");
  }

  private static function ensureInitialized(): void
  {
    if (!isset(self::$registeredAliases['default'])) {
      self::init();
      return;
    }

    self::ensureDebugPanel();
  }

  public static function dispense(string $type): object
  {
    self::ensureInitialized();
    return self::measure('dispense', fn() => R::dispense($type));
  }

  public static function store(object $bean): int|string
  {
    self::ensureInitialized();
    return self::measure('store', fn() => R::store($bean));
  }

  public static function load(string $type, int|string $id): object
  {
    self::ensureInitialized();
    return self::measure('load', fn() => R::load($type, $id));
  }

  public static function find(string $type, ?string $sql = null, array $bindings = []): array
  {
    self::ensureInitialized();
    return self::measure('find', fn() => R::find($type, $sql, $bindings));
  }

  public static function findOne(string $type, ?string $sql = null, array $bindings = []): ?object
  {
    self::ensureInitialized();
    return self::measure('findOne', fn() => R::findOne($type, $sql, $bindings));
  }

  public static function findAll(string $type): array
  {
    self::ensureInitialized();
    return self::measure('findAll', fn() => R::findAll($type));
  }

  public static function exec(string $sql, array $bindings = []): mixed
  {
    self::ensureInitialized();
    return self::measure('exec', fn() => R::exec($sql, $bindings));
  }

  public static function getAll(string $sql, array $bindings = []): array
  {
    self::ensureInitialized();
    return self::measure('getAll', fn() => R::getAll($sql, $bindings));
  }

  /*
   * Retorna linhas em array sem expor SQL no controller.
   * Aceita colunas explicitas ou 'all', com ordenacao opcional por colunas validadas.
   */
  public static function listAsArray(string $type, array|string $columns = 'all', ?array $orderBy = null): array
  {
    self::ensureInitialized();

    $table = self::sanitizeIdentifier($type);
    $columnList = self::buildColumnList($columns);
    $orderClause = self::buildOrderByClause($orderBy);
    $sql = "SELECT {$columnList} FROM {$table}{$orderClause}";

    return self::measure('listAsArray', fn() => R::getAll($sql));
  }

  public static function raw(): RedBeanProxy
  {
    self::ensureInitialized();

    if (self::$proxy === null) {
      self::$proxy = new RedBeanProxy();
    }

    return self::$proxy;
  }

  public static function measureRawCall(string $method, callable $callback): mixed
  {
    self::ensureInitialized();
    return self::measure('raw.' . $method, $callback);
  }

  public static function getDebugStats(): array
  {
    return self::$metrics;
  }

  private static function measure(string $operation, callable $callback): mixed
  {
    if (!self::isDevelopment()) {
      return $callback();
    }

    $startTime = microtime(true);

    try {
      return $callback();
    } finally {
      $duration = microtime(true) - $startTime;
      self::recordMetric($operation, $duration);
    }
  }

  private static function recordMetric(string $operation, float $duration): void
  {
    self::$metrics['totalTime'] += $duration;
    self::$metrics['totalCalls']++;

    if (!isset(self::$metrics['operations'][$operation])) {
      self::$metrics['operations'][$operation] = [
        'calls' => 0,
        'time' => 0.0,
      ];
    }

    self::$metrics['operations'][$operation]['calls']++;
    self::$metrics['operations'][$operation]['time'] += $duration;
  }

  private static function ensureDebugPanel(): void
  {
    if (!self::isDevelopment() || self::$panelAdded) {
      return;
    }

    try {
      $bar = Debugger::getBar();

      if ($bar !== null && !$bar->getPanel(RedBeanPanel::class)) {
        $bar->addPanel(new RedBeanPanel());
      }

      self::$panelAdded = true;
    } catch (Throwable) {
      // Ignora falha de Tracy fora do ciclo HTTP (ex.: CLI).
    }
  }

  private static function isDevelopment(): bool
  {
    return defined('APP_ENV') && APP_ENV === 'development';
  }

  private static function buildColumnList(array|string $columns): string
  {
    if ($columns === 'all') {
      return '*';
    }

    if (count($columns) === 0) {
      throw new \InvalidArgumentException('Informe ao menos uma coluna em listAsArray().');
    }

    return implode(', ', array_map(static fn(string $column): string => self::sanitizeIdentifier($column), $columns));
  }

  private static function buildOrderByClause(?array $orderBy): string
  {
    if ($orderBy === null || count($orderBy) === 0) {
      return '';
    }

    $parts = [];

    foreach ($orderBy as $column => $direction) {
      $safeColumn = self::sanitizeIdentifier((string) $column);
      $safeDirection = strtoupper((string) $direction);

      if (!in_array($safeDirection, ['ASC', 'DESC'], true)) {
        throw new \InvalidArgumentException('Direcao de ordenacao invalida em listAsArray(). Use ASC ou DESC.');
      }

      $parts[] = $safeColumn . ' ' . $safeDirection;
    }

    return ' ORDER BY ' . implode(', ', $parts);
  }

  private static function sanitizeIdentifier(string $identifier): string
  {
    if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $identifier)) {
      throw new \InvalidArgumentException("Identificador SQL invalido em listAsArray(): [$identifier]");
    }

    return '"' . $identifier . '"';
  }

  private static function defaultSqlitePath(): string
  {
    return dirname(__DIR__) . '/writable/db/app.sqlite';
  }
}

/*
 * Proxy para preservar API raw() do RedBean com instrumentação de tempo.
 */
final class RedBeanProxy
{
  public function __call(string $name, array $arguments): mixed
  {
    return RedBeanService::measureRawCall($name, fn() => R::$name(...$arguments));
  }
}

/*
 * Painel Tracy para exibir tempo total e detalhamento por operação do RedBean.
 */
final class RedBeanPanel implements IBarPanel
{
  public function getTab(): string
  {
    $stats = RedBeanService::getDebugStats();
    $calls = (int) ($stats['totalCalls'] ?? 0);
    $totalMs = (float) ($stats['totalTime'] ?? 0) * 1000;

    return "<svg viewBox='0 0 24 24' fill='none' stroke='dodgerblue' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>
      <path stroke='none' d='M0 0h24v24H0z' fill='none' />
      <path d='M12 6m-8 0a8 3 0 1 0 16 0a8 3 0 1 0 -16 0' />
      <path d='M4 6v6a8 3 0 0 0 16 0v-6' />
      <path d='M4 12v6a8 3 0 0 0 16 0v-6' />
    </svg> {$calls} / " . number_format($totalMs, 2) . " ms";
  }

  public function getPanel(): string
  {
    $stats = RedBeanService::getDebugStats();
    $operations = $stats['operations'] ?? [];

    uasort($operations, static fn(array $a, array $b): int => $b['time'] <=> $a['time']);

    ob_start();
    echo '<h1>RedBean</h1>';
    echo '<div>Total time: ' . number_format(((float) ($stats['totalTime'] ?? 0)) * 1000, 2) . ' ms</div>';
    echo '<div>Total calls: ' . (int) ($stats['totalCalls'] ?? 0) . '</div>';
    echo '<table>';
    echo '<tr><th>Operation</th><th>Calls</th><th>Total (ms)</th><th>Avg (ms)</th></tr>';

    foreach ($operations as $operation => $metric) {
      $calls = max(1, (int) ($metric['calls'] ?? 0));
      $totalMs = ((float) ($metric['time'] ?? 0)) * 1000;
      $avgMs = $totalMs / $calls;

      echo '<tr>';
      echo '<td style="font-family:monospace;">' . htmlspecialchars((string) $operation, ENT_QUOTES, 'UTF-8') . '</td>';
      echo '<td>' . $calls . '</td>';
      echo '<td>' . number_format($totalMs, 2) . '</td>';
      echo '<td>' . number_format($avgMs, 2) . '</td>';
      echo '</tr>';
    }

    echo '</table>';
    return ob_get_clean();
  }
}
