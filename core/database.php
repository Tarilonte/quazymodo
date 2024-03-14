<?php

// Using Medoo namespace.
use Medoo\Medoo;

// Função para definir o database
function setDatabase(string $databaseName): Medoo
{
  $database = new Medoo([
    // [required]
    'type' => $_ENV['DB_TYPE'],
    'host' => $_ENV['DB_HOST'],
    'database' => $databaseName,
    'username' => $_ENV['DB_USER'],
    'password' => $_ENV['DB_PASS'],

    // [optional]
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_general_ci',
    'port' => 3306,
  
    // [optional] The table prefix. All table names will be prefixed as PREFIX_table.
    // 'prefix' => 'PREFIX_',
  
    // [optional] To enable logging. It is disabled by default for better performance.
    // 'logging' => true,
  
    // [optional]
    // Error mode
    // Error handling strategies when the error has occurred.
    // PDO::ERRMODE_SILENT (default) | PDO::ERRMODE_WARNING | PDO::ERRMODE_EXCEPTION
    // Read more from https://www.php.net/manual/en/pdo.error-handling.php.
    'error' => PDO::ERRMODE_EXCEPTION,
  
    // [optional]
    // The driver_option for connection.
    // Read more from http://www.php.net/manual/en/pdo.setattribute.php.
    // 'option' => [PDO::ATTR_CASE => PDO::CASE_NATURAL],
  
    // [optional] Medoo will execute those commands after the database is connected.
    // 'command' => ['SET SQL_MODE=ANSI_QUOTES']
  ]);
  // Retorna o objeto database
  return $database;
}

function medoo_insert(Medoo $DB, string $tabela, array $dados): array
{
  try {
    $insert = $DB->insert($tabela, $dados);
  } catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
    die();
  }
  $return['rowCount'] = $insert->rowCount();
  $return['insertId'] = $DB->id();
  return $return;
}