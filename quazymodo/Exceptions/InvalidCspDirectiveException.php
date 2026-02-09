<?php

namespace Quazymodo\Exceptions;

class InvalidCspDirectiveException extends \Exception
{
  public function __construct(string $directive)
  {
    parent::__construct("Diretiva CSP [$directive] nao suportada.");
  }
}
