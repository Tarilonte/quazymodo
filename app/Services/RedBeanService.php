<?php

namespace App\Services;

use RedBeanPHP\R as R;

final class RedBeanService
{
  private static bool $initialized = false;
  private static ?RedBeanProxy $proxy = null;

  public static function init(?string $sqlitePath = null): void
  {
    if (self::$initialized) {
      return;
    }

    $path = $sqlitePath ?? self::defaultSqlitePath();
    R::setup('sqlite:' . $path);

    R::exec('PRAGMA journal_mode = WAL');
    R::exec('PRAGMA synchronous = NORMAL');
    R::exec('PRAGMA temp_store = MEMORY');
    R::exec('PRAGMA foreign_keys = ON');
    R::exec('PRAGMA cache_size = -20000');
    R::exec('PRAGMA busy_timeout = 5000');

    if (defined('APP_ENV') && APP_ENV === 'production') {
      R::freeze(true);
    }

    self::$initialized = true;
  }

  private static function ensureInitialized(): void
  {
    if (!self::$initialized) {
      self::init();
    }
  }

  public static function dispense(string $type): object
  {
    self::ensureInitialized();
    return R::dispense($type);
  }

  public static function store(object $bean): int|string
  {
    self::ensureInitialized();
    return R::store($bean);
  }

  public static function load(string $type, int|string $id): object
  {
    self::ensureInitialized();
    return R::load($type, $id);
  }

  public static function find(string $type, ?string $sql = null, array $bindings = []): array
  {
    self::ensureInitialized();
    return R::find($type, $sql, $bindings);
  }

  public static function findOne(string $type, ?string $sql = null, array $bindings = []): ?object
  {
    self::ensureInitialized();
    return R::findOne($type, $sql, $bindings);
  }

  public static function findAll(string $type): array
  {
    self::ensureInitialized();
    return R::findAll($type);
  }

  public static function exec(string $sql, array $bindings = []): mixed
  {
    self::ensureInitialized();
    return R::exec($sql, $bindings);
  }

  public static function getAll(string $sql, array $bindings = []): array
  {
    self::ensureInitialized();
    return R::getAll($sql, $bindings);
  }

  public static function raw(): RedBeanProxy
  {
    self::ensureInitialized();

    if (self::$proxy === null) {
      self::$proxy = new RedBeanProxy();
    }

    return self::$proxy;
  }

  private static function defaultSqlitePath(): string
  {
    return dirname(__DIR__) . '/writable/db/app.sqlite';
  }
}

final class RedBeanProxy
{
  public function __call(string $name, array $arguments): mixed
  {
    return R::$name(...$arguments);
  }
}
