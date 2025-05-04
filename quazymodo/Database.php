<?php

namespace Quazymodo;

use Medoo\Medoo;
use Tracy\Debugger;

abstract class Database
{
  private static ?array $connectedDatabases = null;

  private static function getConfiguration(string $hostAlias): array
  {
    return DB[$hostAlias];
  }

  private static function connect($hostAlias, $database): Medoo
  {
    $config = self::getConfiguration($hostAlias);
    $config['database'] = $database;

    if (APP_ENV === 'development') {
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

  public static function getSchema(string $hostAlias, string $database): array
  {
      $connection = self::with($hostAlias, $database);
      $schema = [];

      $tables = $connection->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'")->fetchAll();
      $views = $connection->query("SHOW FULL TABLES WHERE Table_type = 'VIEW'")->fetchAll();

      foreach ($tables as $table) {
          $tableName = $table[0];
          $columns = $connection->query("DESCRIBE $tableName")->fetchAll();
          foreach ($columns as $column) {
              $schema['tables'][$tableName][$column['Field']] = "Type: {$column['Type']} / Null: {$column['Null']} / Key: {$column['Key']} / Default: {$column['Default']} / Extra: {$column['Extra']}";
          }
      }

      foreach ($views as $view) {
          $viewName = $view[0];
          $columns = $connection->query("DESCRIBE $viewName")->fetchAll();
          foreach ($columns as $column) {
              $schema['views'][$viewName][$column['Field']] = "Type: {$column['Type']} / Null: {$column['Null']} / Key: {$column['Key']} / Default: {$column['Default']} / Extra: {$column['Extra']}";
          }
      }

      return $schema;
  }
}
