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
    $page = ComponentFactory::create(
      "forms/form-login"
    );
    return $this->html($page);
  }

  public function processLoginForm(ServerRequestInterface $request): ResponseInterface
  {
    sleep(1); // Simulate a delay for the login process
    $page = ComponentFactory::create(
      "js",
      [
        "js" => "login-fail.js",
      ],
      "templateOnly"
    );
    return $this->html($page);
  }
}