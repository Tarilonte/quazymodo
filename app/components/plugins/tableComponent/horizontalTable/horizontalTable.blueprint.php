<?php

/*
|-------------------------------------
| Horizontal Table Component Blueprint
|-------------------------------------
|
| This component receives an array of rows and renders it as a horizontal table.
|
| The caller MUST pass the data (table rows) as an array of associative arrays:
| "rows" => [
|   ['field1' => 'value1', 'field2' => 'value2'],
|   ['field1' => 'value3', 'field2' => 'value4'],
| ]
|
| The header is generated from the first row keys. The caller may override
| headers (rename or hide) with:
| "headers" => [
|   'field1' => 'Field 1',
|   'field2' => false, (set to false to hide any column)
| ]
|
| The caller may pass css classes as follows:
| "table-class" => "class-for-table",
| "thead-class" => "class-for-thead",
| "tbody-class" => "class-for-tbody",
| "tr-class" => "class-for-rows",
| "th-class" => "class-for-headers",
| "td-class" => "class-for-cells"
|
*/

/*
| Set table's css styles
|-----------------------
|*/

$table_class = $inserts['table-class'] ?? "";
$thead_class = $inserts['thead-class'] ?? "";
$tbody_class = $inserts['tbody-class'] ?? "";
$tr_class = $inserts['tr-class'] ?? "";
$th_class = $inserts['th-class'] ?? "text-primary";
$td_class = $inserts['td-class'] ?? "text-base-content";

/*
| Assemble the table's headers and rows
|--------------------------------------
|*/

$rows = $inserts['rows'] ?? [];
$headers_override = $inserts['headers'] ?? [];
$renderedHeader = "";
$renderedRows = "";

if (!empty($rows)) {
  $firstRow = reset($rows);
  $columns = array_keys($firstRow);

  $headerCells = [];
  $visibleColumns = [];

  foreach ($columns as $column) {
    if (array_key_exists($column, $headers_override) && $headers_override[$column] === false) {
      continue;
    }

    $visibleColumns[] = $column;

    $label = $headers_override[$column] ?? $column;
    if ($label === $column) {
      $label = ucwords(str_replace("_", " ", $column));
    }

    $headerCells[] = '<th scope="col" class="' . $th_class . '">' . $label . '</th>';
  }

  $headerRow = Quazymodo\componentFactory::Template(
    "/plugins/tableComponent/horizontalTable/horizontalTableRow",
    [
      "tr-class" => $tr_class,
      "cells" => implode("", $headerCells)
    ],
  );
  $renderedHeader = $headerRow->render();

  $renderedRowList = [];
  foreach ($rows as $row) {
    $cells = [];
    foreach ($visibleColumns as $column) {
      $value = $row[$column] ?? null;
      $value = is_bool($value) ? var_export($value, true) : $value;
      $value = is_null($value) ? "-" : $value;
      $cells[] = '<td class="' . $td_class . '">' . $value . '</td>';
    }

    $componentRow = Quazymodo\componentFactory::Template(
      "/plugins/tableComponent/horizontalTable/horizontalTableRow",
      [
        "tr-class" => $tr_class,
        "cells" => implode("", $cells)
      ],
    );
    $renderedRowList[] = $componentRow->render();
  }

  $renderedRows = implode("\n", $renderedRowList);
}

/*
| Return the blueprint with the rows included
|--------------------------------------------
|*/

return [
  'template' => '/plugins/tableComponent/horizontalTable/horizontalTable',
  'inserts' => [
    'table-class' => $table_class,
    'thead-class' => $thead_class,
    'tbody-class' => $tbody_class,
    'thead-content' => $renderedHeader,
    'tbody-content' => $renderedRows
  ]
];
