<?php

namespace Controller\Test;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response;
use Quazymodo\Component;

use function Quazymodo\Functions\recursiveArraySearch;
use function Quazymodo\Functions\show;

class TestController
{
  public function index(ServerRequestInterface $request): ResponseInterface
  {    
    // Captura o argumento 'method' da URL
    $method = $request->getAttribute('method');

    // Verifica se o método existe na classe
    if (method_exists($this, $method)) {
      // Chama o método dinamicamente
      return $this->$method($request);
    } else {
      // Retorna uma resposta de erro se o método não existir
      die("Método $method não encontrado");
    }
  }

  public function modal(): ResponseInterface
  {    
    $page = new Component("page-modal_test");
    $response = new Response;
    $response->getBody()->write($page->render());
    return $response;
  }

  public function table(): ResponseInterface
  {    
    function readLogFileToArray($filePath) {
        $logEntries = [];
        
        // Verificar se o arquivo existe
        if (file_exists($filePath)) {
            // Abrir o arquivo para leitura
            $fileHandle = fopen($filePath, 'r');
            
            if ($fileHandle) {
                // Ler cada linha do arquivo
                while (($line = fgets($fileHandle)) !== false) {
                    // Decodificar a linha JSON e adicionar ao array principal
                    $logEntries[] = json_decode($line, true);
                }
                // Fechar o arquivo
                fclose($fileHandle);
            } else {
                // Erro ao abrir o arquivo
                throw new Exception("Erro ao abrir o arquivo: $filePath");
            }
        } else {
            // Arquivo não encontrado
            throw new Exception("Arquivo não encontrado: $filePath");
        }
        
        return $logEntries;
    }

    // Caminho para o arquivo de log
    $filePath = '../app/writable/logs/requests.json';

    // Chamar a função e obter o array de logs
    try {
        $logsArray = readLogFileToArray($filePath);
        //show($logsArray);
        //die();
    } catch (Exception $e) {
        echo 'Erro: ' . $e->getMessage();
    }

    // Definir as chaves que você deseja no array
    $keys = ['timestamp', 'ip', 'method', 'path', 'query', 'body', 'status_code', 'execution_time_ms', 'session'];

    // Criar um array vazio com essas chaves
    $tableFields = array_fill_keys($keys, null);

    // Popular o array com os valores correspondentes
    foreach ($tableFields as $key => $value) {
      $tableData[$key] = recursiveArraySearch($logsArray, $key);
    }
  
    // Montar a página
    $page = new Component(
      "page-base",
      [
        "body" => [
          new Component("navbar-01"),
          new Component("table-test", $tableData,  "templateOnly")
        ],
        "navbar-logo" =>  new Component("logo",["logo-class" => "h-8 fill-primary"], componentType: "templateOnly"),
        "navbar-start" =>  "Table Test",
      ]
    );

    // Retornar a página renderizada
    $response = new Response;
    $response->getBody()->write($page->render());
    return $response;
  }
}