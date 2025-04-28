<?php

namespace Controller\Test;

use Quazymodo\AbstractController;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\RequestInterface;
use App\Entities\UserEntity;
use GuzzleHttp\Client;
use Pusher\Pusher;
use Quazymodo\ComponentFactory;
use Quazymodo\CSPManager;
use Quazymodo\Helper;
use Throwable;
use voku\helper\AntiXSS;

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
    return $this->html($page);
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
    } catch (Exception $e) {
        throw new Exception("Erro ao ler o arquivo de log: " . $e->getMessage());
    }

    // Definir as chaves que você deseja no array
    $keys = ['timestamp', 'ip', 'method', 'path', 'query', 'body', 'status_code', 'execution_time_ms', 'session'];

    // Criar um array vazio com essas chaves
    $tableFields = array_fill_keys($keys, null);

    // Popular o array com os valores correspondentes
    foreach ($tableFields as $key => $value) {
      $tableData[$key] = Helper::recursiveArraySearch($logsArray, $key);
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
    return $this->html($page);
  }

  public function error(RequestInterface $request): ResponseInterface|Throwable
  {  
    $errorCode = $request->getQueryParams()['code'] ?? 500;
    throw new \Exception('', $errorCode);
    exit();
  }

  public function htmx(RequestInterface $request): ResponseInterface
  {    
    // Resposta caso haja o argumento 'teste' na URL
    $query = $request->getQueryParams();
    if (isset($query['teste'])) {
      $response = ComponentFactory::create(
        componentName:'test/salsifufu',
        shouldSetNonce: false,
      );
      return $this->html($response);
    }

    // Resposta padrão
    $page = ComponentFactory::create(
      "page-base",
      [
        "js" => "https://unpkg.com/htmx.org@2.0.0",
        "css" => "https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css",
        "body" => [
          ComponentFactory::create("navbar-01"),
          ComponentFactory::create("pages/htmx_test-page", componentType: "templateOnly")
        ],
        'nonce' => CSPManager::getNonce(),
      ]
        );
    return $this->html($page);
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
    $antiXss = new AntiXSS();
    $userInfo = $antiXss->xss_clean($userInfo);

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
    return $this->html($page);
    exit;
    
  }

  public function component(): ResponseInterface
  {    
    $page = ComponentFactory::create("themeSelector-01");
    return $this->html($page);
  }

  public function json_response(): ResponseInterface
  {    
    $array = [
      "name" => "Quazymodo",
      "version" => "1.0.0",
      "description" => "A php quasiframework.",
      "author" => "Your Name",
      "license" => "MIT"
    ];
    return $this->json($array);
  }

  public function daisy(): ResponseInterface
  {     
    $page = ComponentFactory::create(
      "test/daisy"
    );
    return $this->html($page);
  }

  public function alpine(): ResponseInterface
  {     
    $page = ComponentFactory::create(
      "test/alpine"
    );
    return $this->html($page);
  }

  public function toast(): ResponseInterface
  {     
    $page = ComponentFactory::create(
      "test/toast"
    );
    return $this->html($page);
  }

  public function centrifugo(): ResponseInterface
  {     
    $page = ComponentFactory::create(
      componentName:"test/centrifugo"
    );
    return $this->html($page);
  }

  public function soketi(ServerRequestInterface $request): ResponseInterface
  {
    $page = ComponentFactory::create("chatRoom");
    return $this->html($page);
  }

  public function soketiBroadcast(ServerRequestInterface $request): ResponseInterface
  {
      // Decodificando os dados da requisição
      $data = json_decode((string) $request->getBody(), true);

      // Configuraçao do GuzzleHttp Client
      // Desativando a verificação SSL para o cliente GuzzleHttp
      $custom_client = new Client([
          'verify' => false, // Desativa verificação SSL
      ]);
  
      // Configurações do Pusher
      $options = [
          'useTLS' => true,  
          'host' => 'quazymodo',
          'port' => 6001,
          'scheme' => 'https',
          'encrypted' => false,
      ];
  
      // Instanciando o Pusher
      $pusher = new Pusher(
          'app-key', // Substitua pela sua chave do app
          'app-secret', // Substitua pelo seu segredo do app
          'app-id', // Substitua pelo seu ID do app
          $options,
          $custom_client // Passando o cliente GuzzleHttp personalizado
      );
  
      // Dados do evento
      $eventData = [
          'name' => 'chat-message',
          'channel' => 'public-chat',
          'data' => ['message' => $data['message']]
      ];
  
      // Enviando o evento via Pusher
      try {
          $pusher->trigger($eventData['channel'], $eventData['name'], $eventData['data']);
          return $this->json(['status' => 'ok'], 200);
      } catch (\Pusher\PusherException $e) {
          // Em caso de erro
          return $this->json(['status' => 'error', 'message' => $e->getMessage()], 500);
      }
  }
}