<?php

namespace Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Quazymodo\AbstractController;
use Quazymodo\ComponentFactory;
use Quazymodo\CSPManager;

class userController extends AbstractController
{
  public function showLoginForm(ServerRequestInterface $request): ResponseInterface
  {
    $page = componentFactory::Page(
      "/pages/login/form-login"
    );
    return $this->html($page);
  }

  public function processLoginForm(ServerRequestInterface $request): ResponseInterface
  {
    sleep(1); // Simulate a delay for the login process
    $page = componentFactory::Template(
      "/plugins/jsComponent/jsComponent",
      [
        "js" => "login-fail.js",
        "nonce" => CSPManager::getNonce(),
      ]
    );
    return $this->html($page);
  }
}