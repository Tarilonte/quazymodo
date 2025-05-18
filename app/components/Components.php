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