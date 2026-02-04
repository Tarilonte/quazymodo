<?php

namespace App\Services;

use RedBeanPHP\R as R;

final class RedBean
{
  private static bool $initialized = false;

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

  private static function defaultSqlitePath(): string
  {
    return dirname(__DIR__) . '/writable/db/app.sqlite';
  }
}
