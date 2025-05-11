<?php

use Quazymodo\CSPManager;

$fileScript = "/assets" . $inserts['fileScript'];

return [
  'template' => 'plugins/jsComponent/jsComponent',
  'inserts' => [
    "fileScriptSrc" => $fileScript,
    "nonce" => CSPManager::getNonce(),
  ]
];
