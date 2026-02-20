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

  /*
   * Catalog v2 page action.
   *
   * Intencao: expor o novo catalogo com direcao visual editorial.
   */
  public function catalogoV2(): ResponseInterface
  {
    $page = componentFactory::Page(componentName: '/pages/catalogo/v2/');
    return $this->html($page);
  }

  /*
   * Theme colors page action.
   *
   * Intencao: exibir rapidamente as cores semanticas ativas do tema.
   */
  public function tema(): ResponseInterface
  {
    $page = componentFactory::Page(componentName: '/pages/tema/');
    return $this->html($page);
  }

  /*
   * Product page action for Arandela Nina.
   *
   * Intencao: exibir a pagina dedicada de produto com o mesmo tom visual do catalogo.
   */
  public function produtoArandelaNina(): ResponseInterface
  {
    $page = componentFactory::Page(componentName: '/pages/produto/arandela-nina/');
    return $this->html($page);
  }
}
