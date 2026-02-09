<?php

namespace App\Components;

use Quazymodo\BaseComponent;
use Quazymodo\ComponentFactory;

function verticalTable(array $data, array $css = []) : BaseComponent
{
  $array = array_merge(
    ["rows" => $data], 
    $css
  );

  return ComponentFactory::Plugin(
    "/plugins/tableComponent/verticalTable/",
    $array
  );
}

function horizontalTable(array $rows, array $options = []) : BaseComponent
{
  $array = array_merge(
    ["rows" => $rows],
    $options
  );

  return ComponentFactory::Plugin(
    "/plugins/tableComponent/horizontalTable/",
    $array
  );
}

function jsComponent(string $filescript): BaseComponent
{
  return componentFactory::Plugin(
    "/plugins/jsComponent/jsComponent",
    [
      "fileScript" => $filescript
    ]
  );
}
