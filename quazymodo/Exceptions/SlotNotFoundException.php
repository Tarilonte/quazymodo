<?php

namespace Quazymodo\Exceptions;

class SlotNotFoundException extends \Exception
{
  public function __construct(string $component, array $invalidSlots, array $validSlots = [])
  {
    $invalidSlots = array_values(array_unique($invalidSlots));
    sort($invalidSlots);

    $invalid = implode(', ', $invalidSlots);

    if (count($validSlots) > 0) {
      $valid = implode(', ', $validSlots);
      parent::__construct(
        "Slots nao encontrados no componente [$component]: [$invalid].\nSlots validos: [$valid]."
      );
      return;
    }

    parent::__construct(
      "Slots declarados nao foram resolvidos no componente [$component]: [$invalid]."
    );
  }
}
