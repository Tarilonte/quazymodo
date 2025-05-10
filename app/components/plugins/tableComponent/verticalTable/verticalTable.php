<?php

/*
|----------------------------------
| Vertical Table Component Blueprint
|----------------------------------
|
| This Component receives an array with the table rows from the Controller and renders
| it as a vertical table.
|
| The controller MUST pass the data (rows) as an associative array with the following structure:
| "rows" => ['field1' => 'value1', 'field2' => 'value2', ...]
| where every pair 'field' => 'value' will be rendered as a table row.
|
|*/


/*
| Assemble the table's rows
|---------------------------------
|*/

foreach ($controllerData['rows'] as $fieldName => $value) {
  $fieldName = ucwords(str_replace("_", " ", $fieldName));
  $value = is_bool($value) ? var_export($value,true) : $value;
  $componentRow = Quazymodo\componentFactory::Template(
    "/plugins/tableComponent/verticalTable/verticalTableRow",
    [
      "tr-class" => "hover",
      "th-class" => "text-primary",
      "th-content" => $fieldName,
      "td-class" => is_null($value) ? "italic text-base-content/40" : "",
      "td-content" => is_null($value) ? "-" : $value
    ],
  );
  $renderedRow[] = $componentRow->render();
}
$renderedRows = implode("\n", $renderedRow);

/*
| Return the blueprint with the rows included
|---------------------------------
|*/

return [
  'template' => '/plugins/tableComponent/verticalTable/verticalTable',
  'inserts' => [
    'tbody-content' => $renderedRows
  ]
];