<?php

namespace Quazymodo;

class ComponentData
{
  private $blueprintInserts = [];
  private $inserts = [];
  private $merged_data = [];
  public  $declared_slots = [];
  public  $final_data = [];
  public  $js = [];
  public  $css = [];

  public function __construct(array $blueprintInserts = [], array $inserts = [])
  {
    $this->blueprintInserts = $blueprintInserts;
    $this->inserts = $inserts;
    foreach ($this->blueprintInserts as $rawSlot => $content) {
      $this->merge_blueprintInserts($rawSlot, $content);
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
  private function merge_blueprintInserts($rawSlot, $content) : void
  {
    $this->merge_insertEntry($rawSlot, $content);
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
    foreach ($inserts as $rawSlot => $content) {
      if (in_array($rawSlot, ['css', 'js'], true)) {
        foreach ($this->normalize_insertItems($content) as $item) {
          $this->$rawSlot[] = $item;
        }
        continue;
      }

      $this->merge_insertEntry($rawSlot, $content);
    }
  }

  private function merge_insertEntry(string $rawSlot, $content): void
  {
    [$slot, $operation] = $this->parse_slotOperation($rawSlot);
    $items = $this->normalize_insertItems($content);
    $this->apply_insertOperation($slot, $operation, $items);
  }

  private function parse_slotOperation(string $rawSlot): array
  {
    $parts = explode('@', $rawSlot, 2);

    if (
      count($parts) === 2
      && strlen($parts[0]) > 0
      && in_array($parts[1], ['append', 'prepend', 'replace'], true)
    ) {
      return [$parts[0], $parts[1]];
    }

    return [$rawSlot, 'append'];
  }

  private function normalize_insertItems($content): array
  {
    if ($content === null) {
      return [];
    }

    if (is_array($content) && array_is_list($content)) {
      return $content;
    }

    return [$content];
  }

  private function apply_insertOperation(string $slot, string $operation, array $items): void
  {
    $this->track_declaredSlot($slot);

    if (!isset($this->merged_data[$slot])) {
      $this->merged_data[$slot] = [];
    }

    if ($operation === 'replace') {
      $this->merged_data[$slot] = $items;
      return;
    }

    if ($operation === 'prepend') {
      $this->merged_data[$slot] = array_merge($items, $this->merged_data[$slot]);
      return;
    }

    $this->merged_data[$slot] = array_merge($this->merged_data[$slot], $items);
  }

  private function track_declaredSlot(string $slot): void
  {
    if (!in_array($slot, $this->declared_slots, true)) {
      $this->declared_slots[] = $slot;
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
