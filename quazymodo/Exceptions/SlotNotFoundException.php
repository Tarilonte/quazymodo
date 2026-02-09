<?php

namespace Quazymodo\Exceptions;

class SlotNotFoundException extends \Exception
{
  public function __construct(string $component, array $invalidSlots, array $validSlots)
  {
    $invalid = implode(', ', $invalidSlots);
    $valid = implode(', ', $validSlots);

    parent::__construct(
      "Slots nao encontrados no componente [$component]: [$invalid].\nSlots validos: [$valid]."
    );
  }
}
