<?php

namespace Controller;

use Psr\Http\Message\ResponseInterface;
use Quazymodo\AbstractController;
use Quazymodo\ComponentFactory;

/*
 * Development-only component examples controller.
 */
class ExamplesController extends AbstractController
{
  public function index(): ResponseInterface
  {
    // Render the examples page to document reusable UI components.
    $page = ComponentFactory::Page(componentName: '/pages/exemplos/');

    return $this->html($page);
  }
}
