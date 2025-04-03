<?php

namespace Controller\Test;

use Quazymodo\AbstractController;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\RequestInterface;
use App\Entities\UserEntity;
use Quazymodo\ComponentFactory;
use Throwable;

use function Quazymodo\Functions\recursiveArraySearch;

class TestController extends AbstractController
{
  public function index(ServerRequestInterface $request): ResponseInterface
  {    
    // Captura o argumento 'test' da URL
    $test = $request->getAttribute('test');

    // Verifica se o método existe na classe
    if (method_exists($this, $test)) {
      // Chama o método dinamicamente
      return $this->$test($request);
    } else {
      // Retorna uma resposta de erro se o método não existir
      die("Teste $test não encontrado");
    }
  }

  public function modal(): ResponseInterface
  {    
    $page = ComponentFactory::create("page-modal_test");
    return $this->makeHttpResponse($page);
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
    $page = ComponentFactory::create(
      "page-base",
      [
        "body" => [
          ComponentFactory::create("navbar-01"),
          ComponentFactory::create("table-test", $tableData,  "templateOnly")
        ],
        "navbar-logo" =>  ComponentFactory::create("logo",["logo-class" => "h-8 fill-primary"], componentType: "templateOnly"),
        "navbar-start" =>  "Table Test",
      ]
    );
    return $this->makeHttpResponse($page);
  }

  public function error(): ResponseInterface|Throwable
  {  
    throw new \Exception('Tarilonte', 503);
  }

  public function htmx(RequestInterface $request): ResponseInterface
  {    
    $query = $request->getQueryParams();
    if (isset($query['teste'])) {
      $effects = ['rubberBand', 'backInDown', 'bounceInDown', 'heartBeat', 'flip', 'lightSpeedInLeft', 'zoomInUp','jackInTheBox'];
      $effect = $effects[array_rand($effects)];
      exit("<h1 class='font-black text-8xl text-accent animate__animated animate__$effect'>
                ÇA C'EST FOU FOU!!
              </h1>");
    }
    $page = ComponentFactory::create(
      "page-base",
      [
        "css" => "https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css",
        "js" => "https://unpkg.com/htmx.org@2.0.0 [integrity='sha384-wS5l5IKJBvK6sPTKa2WZ1js3d947pvWXbPJ1OmWfEuxLgeHcEbjUUA5i9V5ZkpCw' crossorigin='anonymous']",
        "body" => [
          ComponentFactory::create("navbar-01"),
          ComponentFactory::create("pages/htmx_test-page", componentType: "templateOnly")
          ]
      ]
    );
    return $this->makeHttpResponse($page);
  }

  public function user(RequestInterface $request): ResponseInterface
  {    
    $user = new UserEntity();

    // Verifica se o argumento 'reset' está presente na URL
    if (isset($request->getQueryParams()['reset'])) {
      $user->resetSessionInfo();
    }

    // Obtém as informações do usuário
    $userInfo = $user->get(83);
    //dumpe($userInfo);

    // Monta a tabela com as informações do usuário
    $table = ComponentFactory::create("tables/vertical-table",["rows" => $userInfo]);

    // Monta a página
    $page = ComponentFactory::create(
      "page-base",
      [
        "body" => [
          ComponentFactory::create("navbar-01"),
          $table
        ],
        "navbar-logo" =>  ComponentFactory::create("logo",["logo-class" => "h-8 fill-primary"], componentType: "templateOnly"),
        "navbar-start" =>  "User Info",
      ]
    );
    return $this->makeHttpResponse($page);
    exit;
    
  }

  public function component(): ResponseInterface
  {    
    $page = ComponentFactory::create("themeSelector-01");
    return $this->makeHttpResponse($page);
  }

  public function daisy(): ResponseInterface
  {     
    $page = ComponentFactory::create(
      "page-base",
      [
        "body" => [
          ComponentFactory::create("navbar-01",["navbar-start" => "Daisy Test"]),
          ComponentFactory::create("daisy-test", [],  "templateOnly"),
        ],        
        "navbar-logo" =>  ComponentFactory::create("logo",["logo-class" => "h-8 fill-primary"], componentType: "templateOnly"),
      ]
    );
    return $this->makeHttpResponse($page);
  }
}