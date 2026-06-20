<?php

use Quazymodo\CSPManager;

/*
 * Resolve o script dinamico do jsComponent:
 * - mantem URLs externas como estao;
 * - para arquivos locais, aplica versionamento com filemtime (?v=...).
 */
$fileScriptInput = (string) ($inserts['fileScript'] ?? '');
$fileScriptSource = '';

// Entrada opcional de script dinamico.
if ($fileScriptInput !== '') {
  // URLs externas nao recebem versionamento local.
  if (preg_match('/^(https?:)?\/\//i', $fileScriptInput) === 1) {
    $fileScriptSource = $fileScriptInput;
  } else {
    // Scripts locais recebem ?v=filemtime para bust de cache.
    $relativePath = ltrim($fileScriptInput, '/');
    $resolvedPath = dirname(__DIR__, 4) . '/app/components/' . $relativePath;
    $versionedPath = '/' . $relativePath;

    if (file_exists($resolvedPath)) {
      $versionedPath .= '?v=' . filemtime($resolvedPath);
    }

    $fileScriptSource = '/assets' . $versionedPath;
  }
}

return [
  'template' => 'plugins/jsComponent/jsComponent',
  'inserts' => [
    "fileScriptSrc" => $fileScriptSource
  ]
];
