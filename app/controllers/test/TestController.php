<?php

namespace Controller\Test;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response;
use Quazymodo\Component;

class TestController
{
  public function index(ServerRequestInterface $request): ResponseInterface
  {    
    // Captura o argumento 'method' da URL
    $method = $request->getAttribute('method');

    // Verifica se o método existe na classe
    if (method_exists($this, $method)) {
      // Chama o método dinamicamente
      return $this->$method($request);
    } else {
      // Retorna uma resposta de erro se o método não existir
      die("Método $method não encontrado");
    }
  }

  public function modal(): ResponseInterface
  {    
    $page = new Component("page-modal_test");
    $response = new Response;
    $response->getBody()->write($page->render());
    return $response;
  }

  public function table(): ResponseInterface
  {    
    $page = new Component(
      "page-base",
      [
        "body" => [
          new Component("navbar-01"),
          new Component("table-test", componentType: "templateOnly")
        ],
        "navbar-logo" =>  new Component("logo",["logo-class" => "h-8 fill-primary"], componentType: "templateOnly"),
        "navbar-start" =>  "Table Test",
      ]
    );
    $response = new Response;
    $response->getBody()->write($page->render());
    return $response;
  }
}