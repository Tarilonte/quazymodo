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
}