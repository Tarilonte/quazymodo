<?php
namespace Model;
use Medoo\Medoo;

class Resumo
{
  private static Medoo $database;

  public static function init(): void
  {
    self::$database = setDatabase('RESUMOS');
  }

  public static function getLast(int $limit=10): array|false
  {
    $lista = self::$database->select(
      'VIEW_resumos',
      '*',
      [
        "ORDER" => ["doc_id" => "DESC"],
        "LIMIT" => [0, $limit] 
      ]
    );

    if (self::$database->error) {
      die("Erro: listLast()<br>". self::$database->error);
    }

    if (empty($lista)) {
      return false;
    }
    return $lista ?: false;
  }

  public static function getBy(string $column, mixed $value, int $limit=10): array|false
  {
    switch ($column) {
      case 'doc_id':
        if (filter_var($value, FILTER_VALIDATE_INT) == false) {
          return false;
        }
        break;
    }
    $lista = self::$database->select(
      'VIEW_resumos',
      '*',
      [
        $column => $value,
        "ORDER" => ["doc_id" => "DESC"],
        "LIMIT" => [0, $limit] 
      ]
    );

    if (self::$database->error) {
      die("Erro: listBy()<br>". self::$database->error);
    }

    if (empty($lista)) {
      return false;
    }
    return $lista ?: false;
  }  
}

Resumo::init();