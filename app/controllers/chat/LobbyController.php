<?php

namespace Controller\Chat;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Quazymodo\AbstractController;
use Quazymodo\ComponentFactory;

class LobbyController extends AbstractController
{
  private string $centrifugoApiUrl = 'http://localhost:8000/api'; // URL da API do Centrifugo
  private string $centrifugoApiKey = 'seu_api_key_aqui'; // Substitua pela chave da API do Centrifugo

  public function index(ServerRequestInterface $request): ResponseInterface
  {
    if (isset($_SESSION['nickname'])) {
      return $this->enterLobby($request);
    } else {
      return $this->showLoginForm();
    }
  }

  public function showLoginForm(): ResponseInterface
  {
    $page = ComponentFactory::create(
      "chat/chat-login"
    );
    return $this->html($page);
  }

  public function enterLobby(ServerRequestInterface $request): ResponseInterface
  {
    // Recupera o apelido enviado via POST
    $parsedBody = $request->getParsedBody();
    $nickname = $parsedBody['nickname'] ?? null;

    sleep(1); // Simulate a delay for the login process
    $page = ComponentFactory::create(
      "js",
      [
        "js" => "chat/login-fail.js [defer]",
      ],
      "templateOnly"
    );
    return $this->html($page);
    // Captura a lista de usuários no Lobby
    $onlineUsers = $this->getOnlineUsers();

    // Verifica se o apelido já está em uso
    if (in_array($nickname, $onlineUsers)) {
      $page = ComponentFactory::create(
        "chat/chat-login",
        ["error" => "O apelido '$nickname' já está em uso. Escolha outro."]
      );
      return $this->html($page);
    }

    // Salva o apelido na sessão
    $_SESSION['nickname'] = $nickname;

    // Renderiza o Lobby
    $page = ComponentFactory::create(
      "chat/chat-lobby",
      ["nickname" => $nickname, "onlineUsers" => $onlineUsers]
    );
    return $this->html($page);
  }

  private function getOnlineUsers(): array
  {
    $channel = 'lobby';
    $url = $this->centrifugoApiUrl . '/presence/' . $channel;

    try {
        // Cria um cliente Guzzle
        $client = new \GuzzleHttp\Client();

        // Faz a requisição GET para o Centrifugo
        $response = $client->get($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'apikey ' . $this->centrifugoApiKey,
            ],
        ]);

        // Decodifica a resposta JSON
        $data = json_decode($response->getBody()->getContents(), true);

        // Extrai os apelidos dos usuários conectados
        $users = [];
        if (isset($data['result']['presence'])) {
            foreach ($data['result']['presence'] as $user) {
                $users[] = $user['user']; // Substitua 'user' pelo campo correto, se necessário
            }
        }

        return $users;
    } catch (\Exception $e) {
        // Em caso de erro, retorna uma lista vazia e registra o erro
        error_log('Erro ao obter usuários online: ' . $e->getMessage());
        return [];
    }
  }
}