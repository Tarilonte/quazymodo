<?php

/*
 * Tracy debugger bootstrap.
 */

// Enable Tracy debugger only in development mode.
if (APP_ENV === 'development') {
  $tracyMode = Tracy\Debugger::Development;
  $tracyLogDir = __DIR__ . '/../writable/tracy/';

  Tracy\Debugger::enable($tracyMode, $tracyLogDir);
  Tracy\Debugger::$strictMode = true;
  Tracy\Debugger::$showBar = true;
}
