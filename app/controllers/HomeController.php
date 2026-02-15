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
   * Full sections demo action.
   *
   * Intencao: expor pagina alternativa com 5 secoes 100% viewport.
   */
  public function sections(): ResponseInterface
  {
    $page = componentFactory::Page(componentName: '/pages/home-sections/');
    return $this->html($page);
  }
}
