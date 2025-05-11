<?php

namespace quazymodo;

class ComponentData
{
  private $blueprintInserts = [];
  private $inserts = [];
  private $merged_data = [];
  public  $final_data = [];
  public  $js = [];
  public  $css = [];

  public function __construct(array $blueprintInserts = [], array $inserts = [])
  {
    $this->blueprintInserts = $blueprintInserts;
    $this->inserts = $inserts;
    foreach ($this->blueprintInserts as $slot => $content) {
      $this->merge_blueprintInserts($slot, $content);
    }
    $this->merge_inserts($this->inserts);
    $this->parse_mergedData($this->merged_data);
  }

  /*
  |--------------------------------------------------------------------------
  | merge_blueprintInserts
  |--------------------------------------------------------------------------
  |
  | Processa um elemento de dados do blueprint e o armazena em $merged_data
  |
  |*/
  private function merge_blueprintInserts($slot, $content) : void
  {
    $this->merged_data[$slot][] = $content; 
  }

  /*
  |--------------------------------------------------------------------------
  | merge_inserts
  |--------------------------------------------------------------------------
  |
  | Processa o array de dados recebidos do controller e o armazena em $merged_data
  |
  |*/
  private function merge_inserts($inserts) : void
  {
    foreach ($inserts as $slot => $content) {
      if (is_array($content) && array_is_list($content)) {
        for ($i = 0; $i < count($content); $i++) {
          $this->merge_inserts([$slot => $content[$i]]);         
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