<?php

namespace Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Quazymodo\AbstractController;
use Quazymodo\ComponentFactory;

class userController extends AbstractController
{
  public function showLoginForm(ServerRequestInterface $request): ResponseInterface
  {
    $page = componentFactory::Page(
      "/pages/user/login/"
    );
    return $this->html($page);
  }

  public function showRegistrationForm(ServerRequestInterface $request): ResponseInterface
  {
    $page = componentFactory::Page(
      "/pages/user/register/"
    );
    return $this->html($page);
  }

  public function processLoginForm(ServerRequestInterface $request): ResponseInterface
  {
    sleep(1); // Simulate a delay for the login process
    $page = componentFactory::Plugin(
      "/plugins/jsComponent/",
      [
        "fileScript" => "/pages/user/login/login-fail.js"
      ]
    );
    return $this->html($page);
  }

  public function processRegistrationForm(ServerRequestInterface $request): ResponseInterface
  {
    // Simular um atraso para o processo de registro
    sleep(1); 
    $data = $request->getParsedBody();
    $name = $data['name'] ?? '';
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
    $passwordConfirmation = $data['password_confirmation'] ?? '';

    // TODO: Adicionar validação completa dos dados
    // - Verificar se o e-mail já existe
    // - Verificar se as senhas coincidem
    // - Validar força da senha, formato do e-mail, etc.

    if (empty($name) || empty($email) || empty($password) || $password !== $passwordConfirmation) {
      // Exemplo de como retornar um erro (poderia ser um componente de toast/alerta)
      $page = componentFactory::Plugin(
        "/plugins/jsComponent/",
        [
          "fileScript" => "/pages/user/register/register-fail.js" // Criar este JS para feedback
        ]
      );
      return $this->html($page);
    }

    // TODO: Lógica para criar o usuário no banco de dados
    // Ex: $userEntity = new UserEntity(); $userEntity->setName($name)... userRepository->save($userEntity);

    // Exemplo de como retornar sucesso (poderia ser um redirecionamento ou mensagem)
    // Por enquanto, vamos apenas simular um sucesso e talvez retornar um script.
    // Idealmente, redirecionar para o login ou dashboard.
    $page = componentFactory::Plugin(
        "/plugins/jsComponent/",
        [
          // Poderia ser um script que mostra uma mensagem de sucesso e redireciona
          "fileScript" => "/pages/user/register/register-fail.js"
        ]
    );
    return $this->html($page);
  }
}