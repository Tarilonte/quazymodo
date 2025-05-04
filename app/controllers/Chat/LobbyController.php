<?php

namespace Controller\Chat;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Quazymodo\AbstractController;
use Quazymodo\ComponentFactory;
use Quazymodo\CSPManager;

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
    $response = ComponentFactory::create(
      "js",
      [
        "inlineScript" => "nickname = '$nickname'",
        "js" => "chat/login-fail.js [defer]",
        "nonce" => CSPManager::getNonce()
      ],
      "templateOnly",
      shouldSetNonce:false
    );
    return $this->html($response);
    
  }


}