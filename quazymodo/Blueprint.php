<?php

namespace Quazymodo;

class Blueprint
{
  private array $array;

  public function __construct($componentName, $inserts)
  {
    $this->array = $this->parse_blueprint($componentName, $inserts);
    $this->array = array_merge(['blueprint' => "$componentName.php"], $this->array);
  }

  private function parse_blueprint($componentName, $inserts) : array
  {
    // Load blueprint file
    $blueprint = $this->load_blueprint($componentName, $inserts);

    // Verify if blueprint is extending another blueprint
    if (isset($blueprint['extends'])) {
      $blueprint = $this->extend_blueprint($blueprint['extends'], $blueprint, $inserts);
    }

    // Caso a chave seja css ou js e o valor seja uma string, converte para array
    foreach ($blueprint as $item => $value) {
      if (in_array($item, ['css', 'js']) && is_string($value) && strlen($value)>0) {
      $blueprint[$item] = [$value];
      }
    }

    $this->insertAssetsPath($blueprint, $componentName);

    return $blueprint;
  }

  private function load_blueprint($blueprintName, $inserts) : array
  {
    // If $blueprintName ends with a slash, includes the file with the same name as the last folder in the string
    // E.g. /pages/base/ => /pages/base/base.blueprint.php
    if (substr($blueprintName, -1) === '/') {
      $blueprintName .= basename($blueprintName);
    }
    // Require blueprint file
    if (file_exists("../app/components/$blueprintName.blueprint.php")) {
      return include "../app/components/$blueprintName.blueprint.php";
    }else{
      die("Blueprint [$blueprintName] não encontrado.");
    }
  }

  private function extend_blueprint($parent_blueprint, $child_blueprint, $inserts) : array
  {

  // Load parent blueprint file
  $parent_blueprintFile = $parent_blueprint;

  //$parent_blueprint = $this->load_blueprint($parent_blueprint, $inserts);
  $parent_blueprint = new Blueprint($parent_blueprintFile, $inserts)->array();
  
  // Merge parent blueprint with child blueprint
  foreach ($child_blueprint as $key => $value) {
    if (isset($parent_blueprint[$key])) {
      if (is_array($parent_blueprint[$key]) && is_array($value)) {
        // Se ambos são arrays, mesclar os arrays
        bdump($parent_blueprint[$key]);
        bdump($value);
        $parent_blueprint[$key] = array_merge_recursive($parent_blueprint[$key], $value);
        bdump($parent_blueprint[$key]);
      } elseif (is_array($parent_blueprint[$key])) {
        // Se blueprint é um array e insert não, adicionar o valor ao array do blueprint
        $parent_blueprint[$key][] = $value;
      } else {
        // Se ambos não são arrays, sobrescrever o valor do blueprint com o valor do insert
        $parent_blueprint[$key] = $value;
      }
    } else {
      // Se a chave não existe no blueprint, adicionar a chave e o valor do insert
      $parent_blueprint[$key] = $value;
    }
  }
  
  return $parent_blueprint;
  }

  private function componentPath(string $componentName): string
  {
  // Returns the path until the last slash of the component name
  $lastSlashPosition = strrpos($componentName, '/');
  if ($lastSlashPosition !== false) {
    return substr($componentName, 0, $lastSlashPosition + 1); 
  } else {
    return '';
  }
  }

  public function insertAssetsPath(array &$blueprint, string $componentName): void
  {
    $assetTypes = ['css', 'js', 'img'];

    foreach ($assetTypes as $type) {
      if (isset($blueprint[$type]) && is_array($blueprint[$type])) {
        foreach ($blueprint[$type] as $key => &$pathValue) { // Use & to modify array directly
          // Ensure $pathValue is a string to avoid errors with string functions
          if (!is_string($pathValue)) {
            // Optionally log a warning or skip this invalid entry
            // error_log("Warning: Non-string path found in blueprint[{$type}][{$key}]: " . gettype($pathValue));
            continue;
          }

          // Verify if already has an absolute path (starts with /)
          // or is an external URL (starts with http://, https://)
          // or is a protocol-relative URL (starts with //)
          if (str_starts_with($pathValue, '/') ||
            str_starts_with($pathValue, 'http://') ||
            str_starts_with($pathValue, 'https://') ||
            str_starts_with($pathValue, '//') // Added check for protocol-relative URLs
          ) {
            continue;
          }

          // Prepend component path to relative paths
          $blueprint[$type][$key] = $this->componentPath($componentName) . $pathValue;
        }
        unset($pathValue); // Good practice to unset reference after loop
      }
    }
  }

  // Getters e setters
  public function __get($name): string|array|null
  {
  if (isset($this->array[$name])) {
    return $this->array[$name];
  } else {
    return null;
  }
  }

  public function array()
  {
  return $this->array;
  }

  public function raw()
  {
  return $this->raw;
  }
}