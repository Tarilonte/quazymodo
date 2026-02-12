<?php

/*
 * Tracy debugger bootstrap for development and production.
 */

// Shared writable directory for Tracy logs and HTML reports.
$tracyLogDir = __DIR__ . '/../writable/tracy/';

if (APP_ENV === 'development') {
  // Development: full diagnostics for local debugging.
  Tracy\Debugger::enable(
    mode: Tracy\Debugger::Development,
    logDirectory: $tracyLogDir,
  );
  Tracy\Debugger::$strictMode = true;
  Tracy\Debugger::$showBar = true;
} else {
  // Production: safe output for users, diagnostics kept in logs.
  Tracy\Debugger::enable(
    mode: Tracy\Debugger::Production,
    logDirectory: $tracyLogDir,
  );

  // Record warnings/notices with detailed reports for troubleshooting.
  Tracy\Debugger::$logSeverity = E_WARNING | E_NOTICE;

  // Keep runtime output clean and safe in production requests.
  Tracy\Debugger::$strictMode = false;
  Tracy\Debugger::$showBar = false;
}
