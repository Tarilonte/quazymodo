<?php

namespace Quazymodo\Exceptions;

class BlueprintNotFoundException extends \Exception
{
  public function __construct(string $blueprintName)
  {
    parent::__construct("Blueprint [$blueprintName] nao encontrado.");
  }
}
