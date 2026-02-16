<?php

namespace Controller;

use Psr\Http\Message\ResponseInterface;
use Quazymodo\AbstractController;
use Quazymodo\ComponentFactory;

class HomeController extends AbstractController
{
  /*
   * Main home action.
   *
   * Intencao: manter a home original na rota raiz.
   */
  public function index(): ResponseInterface
  {
    $page = componentFactory::Page(componentName: '/pages/home/');
    return $this->html($page);
  }

  /*
   * Catalog page action.
   *
   * Intencao: expor o catalogo em secoes 100% viewport.
   */
  public function catalogo(): ResponseInterface
  {
    $page = componentFactory::Page(componentName: '/pages/catalogo/');
    return $this->html($page);
  }
}
