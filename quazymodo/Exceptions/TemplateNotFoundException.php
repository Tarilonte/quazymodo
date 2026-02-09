<?php

namespace Quazymodo\Exceptions;

class TemplateNotFoundException extends \Exception
{
  public function __construct(string $path)
  {
    parent::__construct("Template nao encontrado: [$path]");
  }
}
