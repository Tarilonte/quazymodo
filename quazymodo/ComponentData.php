<?php

namespace quazymodo;

use Quazymodo\ComponentFactory;
use function Quazymodo\Functions\recursiveArraySearch;

class ComponentData
{
  private $blueprintInserts = [];
  private $controllerData = [];
  private $merged_data = [];
  public  $final_data = [];
  public  $js = [];
  public  $css = [];

  public function __construct(array $blueprintInserts = [], array $controllerData = [])
  {
    $this->blueprintInserts = $blueprintInserts;
    $this->controllerData = $controllerData;
    foreach ($this->blueprintInserts as $data_piece) {
      $this->merge_blueprintInserts($data_piece);
    }
    $this->merge_controllerData($this->controllerData);
    $this->parse_mergedData($this->merged_data);
    //print_r($this->css);
    //die("xxxxxxxx [ interrupção ] xxxxxxxxxxx");
  }

  /*
  |--------------------------------------------------------------------------
  | merge_blueprintInserts
  |--------------------------------------------------------------------------
  |
  | Processa um elemento de dados do blueprint e o armazena em $merged_data
  |
  |*/
  private function merge_blueprintInserts(array $data_piece) : void
  {
    if (!isset($data_piece['type'])) {
      $this->merged_data[$data_piece['slot']][] = $data_piece['content'];
    } else {
      switch ($data_piece['type']) {
        case 'template':
          $content = [];
          if (isset($data_piece['content']) && is_array($data_piece['content'])) {
            $content = $data_piece['content'];
          }
          $this->merged_data[$data_piece['slot']][] = ComponentFactory::create($data_piece['source'],$content,"templateOnly");
          break;
        case 'string':
          $this->merged_data[$data_piece['slot']][] = $data_piece['content'];
          break;
        case 'env-var':
          $this->merged_data[$data_piece['slot']][] = recursiveArraySearch($_ENV, $data_piece['source']);
          break;
        case 'session-var':
          $this->merged_data[$data_piece['slot']][] = recursiveArraySearch($_SESSION, $data_piece['source']);
          break;
        case 'cookie':
          if (!isset($_COOKIE[$data_piece['source']])) {
            $data_source = "";
          }else {
            $data_source = $_COOKIE[$data_piece['source']];
          }
          $this->merged_data[$data_piece['slot']][] = $data_source;
          break;
        case 'component':
          // TODO: Implementar verificação para impedir que um componente não seja incluído dentro dele mesmo
          $content = [];
          if (isset($data_piece['content']) && is_array($data_piece['content'])) {
            $content = $data_piece['content'];
          }
          $this->merged_data[$data_piece['slot']][] = ComponentFactory::create($data_piece['source'],$content);
          break;
        case 'array':
          foreach ($data_piece['content'] as $array_item) {
            $this->merge_blueprintInserts($array_item);
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