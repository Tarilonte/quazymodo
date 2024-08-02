<?php

namespace quazymodo;

use Quazymodo\ComponentFactory;
use function Quazymodo\Functions\recursiveArraySearch;

class ComponentData
{
  private $blueprintData = [];
  private $controllerData = [];
  private $merged_data = [];
  public  $final_data = [];
  public  $js = [];
  public  $css = [];

  public function __construct(array $blueprintData = [], array $controllerData = [])
  {
    $this->blueprintData = $blueprintData;
    $this->controllerData = $controllerData;
    foreach ($this->blueprintData as $data_piece) {
      $this->merge_blueprintData($data_piece);
    }
    $this->merge_controllerData($this->controllerData);
    $this->parse_mergedData($this->merged_data);
    //print_r($this->css);
    //die("xxxxxxxx [ interrupção ] xxxxxxxxxxx");
  }

  /*
  |--------------------------------------------------------------------------
  | merge_blueprintData
  |--------------------------------------------------------------------------
  |
  | Processa um elemento de dados do blueprint e o armazena em $merged_data
  |
  |*/
  private function merge_blueprintData(array $data_piece) : void
  {
    if (!isset($data_piece['data-type'])) {
      $this->merged_data[$data_piece['data-slot']][] = $data_piece['data-content'];
    } else {
      switch ($data_piece['data-type']) {
        case 'template':
          $content = [];
          if (isset($data_piece['data-content']) && is_array($data_piece['data-content'])) {
            $content = $data_piece['data-content'];
          }
          $this->merged_data[$data_piece['data-slot']][] = ComponentFactory::create($data_piece['data-source'],$content,"templateOnly");
          break;
        case 'string':
          $this->merged_data[$data_piece['data-slot']][] = $data_piece['data-content'];
          break;
        case 'env-var':
          $this->merged_data[$data_piece['data-slot']][] = recursiveArraySearch($_ENV, $data_piece['data-source']);
          break;
        case 'session-var':
          $this->merged_data[$data_piece['data-slot']][] = recursiveArraySearch($_SESSION, $data_piece['data-source']);
          break;
        case 'cookie':
          if (!isset($_COOKIE[$data_piece['data-source']])) {
            $data_source = "";
          }else {
            $data_source = $_COOKIE[$data_piece['data-source']];
          }
          $this->merged_data[$data_piece['data-slot']][] = $data_source;
          break;
        case 'component':
          // TODO: Implementar verificação para impedir que um componente não seja incluído dentro dele mesmo
          $content = [];
          if (isset($data_piece['data-content']) && is_array($data_piece['data-content'])) {
            $content = $data_piece['data-content'];
          }
          $this->merged_data[$data_piece['data-slot']][] = ComponentFactory::create($data_piece['data-source'],$content);
          break;
        case 'array':
          foreach ($data_piece['data-content'] as $array_item) {
            $this->merge_blueprintData($array_item);
          }
          break;
      }
    }
  }

  /*
  |--------------------------------------------------------------------------
  | merge_controllerData
  |--------------------------------------------------------------------------
  |
  | Processa o array de dados recebidos do controller e o armazena em $merged_data
  |
  |*/
  private function merge_controllerData($controllerData) : void
  {
    foreach ($controllerData as $slot => $content) {
      if (is_array($content) && array_is_list($content)) {
        for ($i = 0; $i < count($content); $i++) {
          $this->merge_controllerData([$slot => $content[$i]]);         
        }
      }elseif(in_array($slot, ['css', 'js'])){
          $this->$slot[] = $content;
      }else{
        $this->merged_data[$slot][] = $content;
      } 
    }
  }

  private function parse_mergedData(array $merged_data) : void
  {
    foreach ($merged_data as $slot => $content) {
      if ( $content instanceof BaseComponent) {
        $this->parse_childComponent($slot, $content);
      } elseif (is_array($content) && array_is_list($content)) {
        for ($i = 0; $i < count($content); $i++) {
          $this->parse_mergedData([$slot => $content[$i]]);         
          }
      } else {
          $this->final_data[$slot][] = $content;
      }
    }
  }

  private function parse_childComponent(string $key, BaseComponent $component) : void
  {
    isset($component->js)? $this->push_asset("js", $component->js) : '';
    isset($component->css)? $this->push_asset("css", $component->css) : '';
    $this->final_data[$key][] = $component->html;
  }

  private function push_asset(string $assetName, array $sources) : void
  {
    foreach ($sources as $source) {
      $this->$assetName[] = $source;
    }
  }
}