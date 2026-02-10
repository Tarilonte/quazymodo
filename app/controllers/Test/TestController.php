<?php

namespace Controller\Test;

use Quazymodo\AbstractController;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\RequestInterface;
use App\Entities\UserEntity;
use App\Services\RedBeanService as RedBean;
use GuzzleHttp\Client;
use Pusher\Pusher;
use Quazymodo\ComponentFactory;
use Quazymodo\CSPManager;
use Quazymodo\Csrf;
use Quazymodo\Helper;
use Reflection;
use ReflectionClass;
use Throwable;
use Tracy\Debugger;
use voku\helper\AntiXSS;

use function App\Components\horizontalTable;
use function App\Components\verticalTable;

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
      sleep(2);
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

  public function user(): ResponseInterface
  {    
    // Obtém as informações do usuário
    $user = new UserEntity();
    $userInfo = $user->get(83);

    // Monta a página
    $page = componentFactory::Page(
      "/pages/test-pages/user/",
      [
        "userInfo" => $userInfo
      ]
    );
    return $this->html($page);    
  }

  public function base2(): ResponseInterface
  {    
    // Monta a página
    $page = componentFactory::Page(
      "/pages/test-pages/base-2/"
    );
    return $this->html($page);    
  }

  public function base3(): ResponseInterface
  {    
    // Monta a página
    $page = componentFactory::Page(
      "/pages/base/base-3"
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

  public function redbean(ServerRequestInterface $request): ResponseInterface
  {
    $message = '';
    $messageType = 'hidden';
    $nameValue = '';
    $emailValue = '';

    if (strtoupper($request->getMethod()) === 'POST') {
      $data = $request->getParsedBody() ?? [];
      $name = trim((string) ($data['name'] ?? ''));
      $email = trim((string) ($data['email'] ?? ''));

      $nameValue = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
      $emailValue = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');

      if ($name === '' || $email === '') {
        $messageType = 'alert alert-error';
        $message = 'Preencha nome e e-mail.';
      } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $messageType = 'alert alert-error';
        $message = 'E-mail inválido.';
      } else {
        $lead = RedBean::dispense('contact');
        $lead->name = $name;
        $lead->email = $email;
        $lead->created_at = date('Y-m-d H:i:s');
        RedBean::store($lead);

        $messageType = 'alert alert-success';
        $message = 'Cadastro realizado com sucesso.';
        $nameValue = '';
        $emailValue = '';
      }
    }

    $page = componentFactory::Page(
      '/pages/test-pages/redbean/',
      [
        'page-title' => 'Teste RedBean',
        'message' => $message,
        'message-type' => $messageType,
        'name-value' => $nameValue,
        'email-value' => $emailValue,
      ]
    );

    return $this->html($page);
  }

  public function redbeanList(ServerRequestInterface $request): ResponseInterface
  {
    $isHtmxRequest = strtolower($request->getHeaderLine('HX-Request')) === 'true';

    if (strtoupper($request->getMethod()) === 'POST') {
      $data = $request->getParsedBody() ?? [];
      $csrfToken = (string) ($data['csrf-token'] ?? '');
      $deleteId = (int) ($data['delete_id'] ?? 0);

      if (!Csrf::verifyToken($csrfToken)) {
        if ($isHtmxRequest) {
          return $this->htmxDeleteResponse(false, 'Token CSRF invalido. Atualize a pagina e tente novamente.', 'error');
        }
      } elseif ($deleteId <= 0) {
        if ($isHtmxRequest) {
          return $this->htmxDeleteResponse(false, 'Contato invalido para exclusao.', 'warning');
        }
      } else {
        $contact = RedBean::load('contact', $deleteId);

        if ((int) ($contact->id ?? 0) > 0) {
          RedBean::raw()->trash($contact);
          if ($isHtmxRequest) {
            return $this->htmxDeleteResponse(true, 'Contato excluido com sucesso.', 'success', $deleteId);
          }
        } elseif ($isHtmxRequest) {
          return $this->htmxDeleteResponse(false, 'Contato nao encontrado.', 'warning');
        }
      }
    }

    $csrfToken = htmlspecialchars(Csrf::setToken(), ENT_QUOTES, 'UTF-8');

    $contacts = RedBean::findAll('contact');
    $contacts = RedBean::raw()->exportAll($contacts);
    $rows = [];

    foreach ($contacts as $contact) {
      $id = (int) ($contact['id'] ?? 0);

      $deleteForm = ComponentFactory::Template(
        '/pages/test-pages/redbean/delete-contact-form',
        [
          'delete-id' => (string) $id,
          'csrf-token' => $csrfToken,
        ]
      );

      $rows[] = [
        'id' => htmlspecialchars((string) $id, ENT_QUOTES, 'UTF-8'),
        'name' => htmlspecialchars((string) ($contact['name'] ?? ''), ENT_QUOTES, 'UTF-8'),
        'email' => htmlspecialchars((string) ($contact['email'] ?? ''), ENT_QUOTES, 'UTF-8'),
        'created_at' => htmlspecialchars((string) ($contact['created_at'] ?? ''), ENT_QUOTES, 'UTF-8'),
        'actions' => $deleteForm->render()
      ];
    }

    $table = horizontalTable(
      $rows,
      [
        'headers' => [
          'id' => 'ID',
          'name' => 'Nome',
          'email' => 'E-mail',
          'created_at' => 'Criado em',
          'actions' => 'Acoes'
        ]
      ]
    );

    $page = componentFactory::Page(
      '/pages/test-pages/redbean/redbean-list',
      [
        'table' => $table->render(),
        'total' => count($contacts)
      ]
    );

    return $this->html($page);
  }

  private function htmxDeleteResponse(bool $success, string $message, string $toastType, ?int $deletedId = null): ResponseInterface
  {
    $payload = [
      'success' => $success,
      'message' => $message,
      'type' => $toastType,
      'deletedId' => $deletedId,
    ];

    $jsonPayload = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $inlineScript = "window.__redbeanDeletePayload = $jsonPayload;";

    $response = ComponentFactory::Plugin(
      '/plugins/jsComponent/',
      [
        'inlineScript' => $inlineScript,
        'fileScript' => '/pages/test-pages/redbean/toast-delete.js',
      ]
    );

    return $this->html($response);
  }

}
