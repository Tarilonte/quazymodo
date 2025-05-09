<?php

namespace Quazymodo;

class ComponentFactory
{
  public static function Page(
    $componentName, 
    $controllerData = []
    ) : BaseComponent
  {
    return self::return($componentName, $controllerData, componentType:'page');
  }

  public static function Component(
    $componentName, 
    $controllerData = [], 
    ) : BaseComponent
  {
    return self::return($componentName, $controllerData, componentType:'component');
  }

  public static function Template(
    $componentName, 
    $controllerData = [], 
    ) : BaseComponent
  {
    return self::return($componentName, $controllerData, componentType:'template');
  }

  private static function return($componentName, $controllerData, $componentType) : BaseComponent
  {
    if (APP_ENV === 'development') {
      return new ComponentDebug($componentName, $controllerData, $componentType);
    }
    return new BaseComponent($componentName, $controllerData, $componentType);
  }

}