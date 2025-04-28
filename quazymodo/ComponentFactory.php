<?php

namespace Quazymodo;

class ComponentFactory
{
  public static function create(
    $componentName, 
    $controllerData = [], 
    $componentType = "component", 
    $shouldSetNonce = true
    ) : BaseComponent
  {
    if (APP_ENV === 'development') {
      return new ComponentDebug($componentName, $controllerData, $componentType, $shouldSetNonce);
    }
    return new BaseComponent($componentName, $controllerData, $componentType, $shouldSetNonce);
  }
}
