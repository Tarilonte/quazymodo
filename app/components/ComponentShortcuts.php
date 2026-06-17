<?php

namespace App\Components;

use Quazymodo\BaseComponent;
use Quazymodo\ComponentFactory;

/**
 * Component shortcuts for common plugin factories.
 */
final class ComponentShortcuts
{
  /**
   * Creates a vertical table plugin component.
   */
  public static function verticalTable(array $rows, array $options = []): BaseComponent
  {
    // Merge defaults with caller options to keep API compact.
    $payload = array_merge([
      'rows' => $rows,
    ], $options);

    return ComponentFactory::Plugin('/plugins/tableComponent/verticalTable/', $payload);
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

    return ComponentFactory::Plugin('/plugins/tableComponent/horizontalTable/', $payload);
  }

  /**
   * Creates the JavaScript plugin wrapper component.
   * Accepts external/local script path, inline script, or both.
   */
  public static function jsComponent(?string $fileScript = null, ?string $inlineScript = null): BaseComponent
  {
    // Build only the keys provided by the caller.
    $payload = [];

    if ($inlineScript !== null && $inlineScript !== '') {
      $payload['inlineScript'] = $inlineScript;
    }

    if ($fileScript !== null && $fileScript !== '') {
      $payload['fileScript'] = $fileScript;
    }

    return ComponentFactory::Plugin('/plugins/jsComponent/jsComponent', $payload);
  }

  /**
   * Creates the temporary environment selector plugin component.
   */
  public static function environmentSelector(): BaseComponent
  {
    // Keep navbar usage explicit while still offering a reusable shortcut.
    return ComponentFactory::Plugin(componentName: '/plugins/environmentSelector/');
  }
}
