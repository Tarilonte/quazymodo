<?php

function adminer_object()
{
  require __DIR__ . '/plugins/CSqlite.php';

  $dbPath = realpath(__DIR__ . '/../../writable/db') ?: (__DIR__ . '/../../writable/db');
  $writablePath = realpath(__DIR__ . '/../../writable') ?: (__DIR__ . '/../../writable');

  $plugins = [];
  $plugins[] = new CSqlite([
    'vPath' => $dbPath,
    'vSearch' => '#(.+\.sqlite|.+\.db)$#',
    'vPwdFile' => $writablePath . '/adminer/CSqlite.pwd',
  ]);

  class AdminerCustomization extends Adminer\Plugins
  {
  }

  return new AdminerCustomization($plugins);
}

include __DIR__ . '/adminer.php';
