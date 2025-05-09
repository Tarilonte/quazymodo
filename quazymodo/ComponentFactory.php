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
    return self::returnComponent($componentName, $controllerData, $componentType, $shouldSetNonce);
  }

  public static function loadTemplate(
    $componentName, 
    $controllerData = [], 
    $componentType = "templateOnly", 
    $shouldSetNonce = false
    ) : BaseComponent
  {
    return self::returnComponent($componentName, $controllerData, $componentType, $shouldSetNonce);
  }

  private static function returnComponent($componentName, $controllerData, $componentType, $shouldSetNonce) : BaseComponent
  {
    if (APP_ENV === 'development') {
      return new ComponentDebug($componentName, $controllerData, $componentType, $shouldSetNonce);
    }
    return new BaseComponent($componentName, $controllerData, $componentType, $shouldSetNonce);
  }

}