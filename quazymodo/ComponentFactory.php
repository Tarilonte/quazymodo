<?php

namespace Quazymodo;

class ComponentFactory
{
  /**
   * Creates a page component. 
   * Pages include 'nonce' CSP header automatically.
   * 
   * @param mixed $componentName 
   * @param array $inserts 
   * @return BaseComponent 
   */
  public static function Page(
    $componentName, 
    $inserts = []
    ) : BaseComponent
  {
    return self::return($componentName, $inserts, componentType:'page');
  }

  public static function Plugin(
    $componentName, 
    $inserts = [], 
    ) : BaseComponent
  {
    return self::return($componentName, $inserts, componentType:'plugin');
  }

  /**
   * Templates doesn't have a blueprint.
   * Loads directly from a html file
   */
  public static function Template(
    $componentName, 
    $inserts = [], 
    ) : BaseComponent
  {
    return self::return($componentName, $inserts, componentType:'template');
  }

  private static function return($componentName, $inserts, $componentType) : BaseComponent
  {
    if (APP_ENV === 'development') {
      return new ComponentDebug($componentName, $inserts, $componentType);
    }
    return new BaseComponent($componentName, $inserts, $componentType);
  }

}