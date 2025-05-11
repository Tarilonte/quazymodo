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
      "/pages/login/"
    );
    return $this->html($page);
  }

  public function processLoginForm(ServerRequestInterface $request): ResponseInterface
  {
    sleep(1); // Simulate a delay for the login process
    $page = componentFactory::Plugin(
      "/plugins/jsComponent/",
      [
        "fileScript" => "/pages/login/login-fail.js"
      ]
    );
    return $this->html($page);
  }
}