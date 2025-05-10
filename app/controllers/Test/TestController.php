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
use Reflection;
use ReflectionClass;
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
    $page = componentFactory::Page("page-modal_test");
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
      $response = componentFactory::Plugin(
        componentName:'/pages/test-pages/htmx/salsifufu/salsifufu'
      );
      return $this->html($response);
    }

    // Resposta padrão
      $page = componentFactory::Page(
        "/pages/test-pages/htmx/htmx"
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
    $table = componentFactory::Plugin(
      "/plugins/tableComponent/verticalTable/verticalTable",
      ["rows" => $userInfo]
    );

    // Monta a página
    $page = componentFactory::Page(
      "/pages/base/base",
      [
        "body" => [
          componentFactory::Plugin("/plugins/navbar/navbar-01"),
          $table
        ],
        "navbar-logo" =>  componentFactory::Template("/plugins/logo/logo",["logo-class" => "h-8 fill-primary"]),
        "navbar-start" =>  "User Info",
      ]
    );
    return $this->html($page);
    exit;
    
  }

  public function component(): ResponseInterface
  {    
    $page = componentFactory::Plugin("themeSelector-01");
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
    $page = componentFactory::Page(
      "test/daisy"
    );
    return $this->html($page);
  }

  public function alpine(): ResponseInterface
  {     
    $page = componentFactory::Page(
      "test/alpine"
    );
    return $this->html($page);
  }

  public function toast(): ResponseInterface
  {     
    $page = componentFactory::Page(
      "pages/test-pages/toast/toast"
    );
    return $this->html($page);
  }

  public function centrifugo(): ResponseInterface
  {     
    $page = componentFactory::Page(
      componentName:"test/centrifugo"
    );
    return $this->html($page);
  }

  public function soketi(ServerRequestInterface $request): ResponseInterface
  {
    $page = componentFactory::Page("chatRoom");
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

  public function sse()
  {
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header('Connection: keep-alive');
    
    while (true) {
      $message = 'event: horacerta' . PHP_EOL;
      $message .= 'data: ' . date('H:i:s') . PHP_EOL . PHP_EOL;
      echo $message;
      ob_flush();
      flush();
      sleep(1);
    }  
  }

  public function list()
  {
    $reflection = new ReflectionClass($this);
    $metodos = $reflection->getMethods();

    foreach ($metodos as $metodo) {
        $name = $metodo->getName();
        $modifiers = implode(', ', Reflection::getModifierNames($metodo->getModifiers()));
        echo "<a href='test/$name'>$name</a> - $modifiers <br>";
        
        
    }
    exit;
  }

  public function emoji()
  {
    $page = componentFactory::Page(
      "page-base",
      [
        "js" => [
          "https://cdn.jsdelivr.net/npm/emoji-mart@latest/dist/browser.js",
          "emoji-mart.js"
        ],
        "body" => [
          componentFactory::Plugin("navbar-01"),
          '<div id="content" class="grow flex justify-center items-center"></div>'
        ],
        "body-class" => "flex flex-col"
      ]
        );
    return $this->html($page);
  }
}