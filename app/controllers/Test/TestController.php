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
use Tracy\Debugger;
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

    // Obtém as informações do usuário
    $userInfo = $user->get(83);
    $antiXss = new AntiXSS();
    $userInfo = $antiXss->xss_clean($userInfo);

    // Monta a tabela com as informações do usuário
    $table = componentFactory::Plugin(
      "/plugins/tableComponent/verticalTable/",
      ["rows" => $userInfo]
    );

    // Monta a página
    $page = componentFactory::Page(
      "/pages/test-pages/user/",
      [
        "table" => $table,
      ]
    );
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

  public function toast(): ResponseInterface
  {     
    $page = componentFactory::Page(
      "/pages/test-pages/toast/"
    );
    return $this->html($page);
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

}