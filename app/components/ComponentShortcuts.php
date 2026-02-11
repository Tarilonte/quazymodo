<?php

namespace App\Components;

use Quazymodo\BaseComponent;
use Quazymodo\ComponentFactory;

/**
 * Component shortcuts for common plugin factories.
 */
final class ComponentShortcuts
{
  private const VERTICAL_TABLE_PLUGIN = '/plugins/tableComponent/verticalTable/';
  private const HORIZONTAL_TABLE_PLUGIN = '/plugins/tableComponent/horizontalTable/';
  private const JS_COMPONENT_PLUGIN = '/plugins/jsComponent/jsComponent';

  /**
   * Creates a vertical table plugin component.
   */
  public static function verticalTable(array $rows, array $options = []): BaseComponent
  {
    // Merge defaults with caller options to keep API compact.
    $payload = array_merge([
      'rows' => $rows,
    ], $options);

    return ComponentFactory::Plugin(self::VERTICAL_TABLE_PLUGIN, $payload);
  }

  /**
   * Creates a horizontal table plugin component.
   */
  public static function horizontalTable(array $rows, array $options = []): BaseComponent
  {
    // Merge defaults with caller options to keep API compact.
    $payload = array_merge([
      'rows' => $rows,
    ], $options);

    return ComponentFactory::Plugin(self::HORIZONTAL_TABLE_PLUGIN, $payload);
  }

  /**
   * Creates the JavaScript plugin wrapper component.
   */
  public static function jsComponent(string $fileScript): BaseComponent
  {
    return ComponentFactory::Plugin(self::JS_COMPONENT_PLUGIN, [
      'fileScript' => $fileScript,
    ]);
  }
}
