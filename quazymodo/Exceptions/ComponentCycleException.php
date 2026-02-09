<?php

namespace Quazymodo\Exceptions;

class ComponentCycleException extends \Exception
{
  public static function cycleDetected(array $activeStack, string $currentComponent): self
  {
    $chain = implode(' -> ', array_merge($activeStack, [$currentComponent]));
    return new self("Chamada de componentes em loop detectada: [$chain]");
  }

  public static function depthExceeded(array $activeStack, string $currentComponent, int $maxDepth): self
  {
    $chain = implode(' -> ', array_merge($activeStack, [$currentComponent]));
    return new self("Profundidade maxima de componentes excedida ($maxDepth): [$chain]");
  }
}
