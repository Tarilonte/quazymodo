<?php

namespace Quazymodo;

use Medoo\Medoo;
use Tracy\Debugger;

abstract class Database
{
  private static ?array $connectedDatabases = null;

  private static function getConfiguration(string $hostAlias): array
  {
    $availableHosts = require __DIR__ . '/../app/dbHosts.php';
    return $availableHosts[$hostAlias];
  }

  private static function connect($hostAlias, $database): Medoo
  {
    $config = self::getConfiguration($hostAlias);
    $config['database'] = $database;

    if ($_ENV['APP_ENV'] === 'development') {
      self::$connectedDatabases[$hostAlias][$database] = new MedooDebug($config, $hostAlias, $database);
      if (!Debugger::getBar()->getPanel('Quazymodo\MedooPanel')) {
        Debugger::getBar()->addPanel(new MedooPanel());
      }
    } else {
      self::$connectedDatabases[$hostAlias][$database] = new Medoo($config);
    }
    return self::$connectedDatabases[$hostAlias][$database];
  }

  public static function with(string $hostAlias, string $database): Medoo
  {
    if (isset(self::$connectedDatabases[$hostAlias][$database])) {
      return self::$connectedDatabases[$hostAlias][$database];
    } else {
      return self::connect($hostAlias, $database);
    }
  }
}
