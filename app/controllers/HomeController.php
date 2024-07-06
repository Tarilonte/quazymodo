<?php

namespace Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response;
use Quazymodo\Component;

use function Quazymodo\Functions\show;

class HomeController extends AbstractController
{
  public function index(ServerRequestInterface $request): ResponseInterface
  {    
    $query = $request->getQueryParams();
    $page = new Component(
      "page-home"
    );

    //show($page->html);
    //die();

    return $this->render($page);
  }
}