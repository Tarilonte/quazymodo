<?php

/*
|-----------------------------------
| Vertical Table Component Blueprint
|-----------------------------------
|
| This Component receives an array with the table rows and renders it as a vertical table.
|
| The caller MUST pass the data (table rows) as an associative array with the following structure:
| "rows" => ['field1' => 'value1', 'field2' => 'value2', ...]
| where every pair 'field' => 'value' will be rendered as a table row.
| The field name will be rendered as a table header (<th>) and the value as a table cell (<td>).
|
| The caller may pass css classes for the table rows, headers and cells as follows:
| "tr-class" => "class-for-rows",
| "th-class" => "class-for-headers",
| "td-class" => "class-for-cells"
|
|*/

/*
| Set table's css styles
|-----------------------
|*/

$tr_class = $inserts['tr-class'] ?? "";
$th_class = $inserts['th-class'] ?? "text-primary";
$td_class = $inserts['td-class'] ?? "text-base-content";

/*
| Assemble the table's rows
|--------------------------
|*/

foreach ($inserts['rows'] as $fieldName => $value) {
  $fieldName = ucwords(str_replace("_", " ", $fieldName));
  $value = is_bool($value) ? var_export($value,true) : $value;
  $componentRow = Quazymodo\componentFactory::Template(
    "/plugins/tableComponent/verticalTable/verticalTableRow",
    [
      "tr-class" => $tr_class,
      "th-class" => $th_class,
      "th-content" => $fieldName,
      "td-class" => $td_class,
      "td-content" => is_null($value) ? "-" : $value
    ],
  );
  $renderedRow[] = $componentRow->render();
}
$renderedRows = implode("\n", $renderedRow);

/*
| Return the blueprint with the rows included
|--------------------------------------------
|*/

return [
  'template' => '/plugins/tableComponent/verticalTable/verticalTable',
  'inserts' => [
    'tbody-content' => $renderedRows
  ]
];