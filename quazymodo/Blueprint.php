<?php

namespace Quazymodo;

class Blueprint
{
  private array $array;

  public function __construct($componentName, $controllerData)
  {
    $this->array = $this->parse_blueprint($componentName, $controllerData);
    $this->array = array_merge(['blueprint' => "$componentName.php"], $this->array);
  }

  private function parse_blueprint($componentName, $controllerData) : array
  {
    // Load blueprint file
    $blueprint = $this->load_blueprint($componentName, $controllerData);

    // Verify if blueprint is extending another blueprint
    if (isset($blueprint['extends'])) {
      $blueprint = $this->extend_blueprint($blueprint['extends'], $blueprint, $controllerData);
    }

    foreach ($blueprint as $item => $value) {
      // Caso a chave seja css ou js e o valor seja uma string, converte para array
      if (in_array($item, ['css', 'js']) && is_string($value)) {
        if (strlen($value)>0) {
          $blueprint[$item] = [$value];
        }
      }
    }
    return $blueprint;
  }

  private function load_blueprint($componentName, $controllerData) : array
  {
    // Require blueprint file
    if (file_exists("../app/components/blueprints/$componentName.php")) {
      return include "../app/components/blueprints/$componentName.php";
    }else{
      die("Blueprint [$componentName] não encontrado.");
    }
  }

  private function extend_blueprint($parent_blueprint, $child_blueprint, $controllerData) : array
  {
    // Load parent blueprint file
    $parent_blueprint = $this->load_blueprint($parent_blueprint, $controllerData);
    
    // Extend parent blueprint with child blueprint
    foreach ($child_blueprint as $key => $value) {
      if (isset($parent_blueprint[$key])) {
          if (is_array($parent_blueprint[$key]) && is_array($value)) {
              // Se ambos são arrays, mesclar os arrays
              $parent_blueprint[$key] = array_merge($parent_blueprint[$key], $value);
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