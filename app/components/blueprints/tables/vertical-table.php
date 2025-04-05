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
  $componentRow = Quazymodo\ComponentFactory::create(
    "tables/vertical-table-tr",
    [
      "tr-class" => "hover",
      "th-class" => "text-primary",
      "th-content" => $fieldName,
      "td-class" => is_null($value) ? "italic text-base-content/40" : "",
      "td-content" => is_null($value) ? "-" : $value
    ],
    "templateOnly"
  );
  $renderedRow[] = $componentRow->render();
}
$renderedRows = implode("\n", $renderedRow);

/*
| Return the blueprint with the rows included
|---------------------------------
|*/

return [
  'template' => 'tables/vertical-table',
  'inserts' => [
    [
      'slot' => 'tbody-content',
      'content' => $renderedRows
    ]
  ]
];