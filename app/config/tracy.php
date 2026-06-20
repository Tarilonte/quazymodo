<?php

/*
 * Tracy debugger bootstrap for development and production.
 */

// Separate CLI logs from web logs to avoid cross-user file collisions.
$tracyBaseLogDir = __DIR__ . '/../writable/tracy/';
$tracyLogDir = PHP_SAPI === 'cli'
  ? $tracyBaseLogDir . 'cli/'
  : $tracyBaseLogDir;

/*
 * Keep Tracy log directory available outside container bootstrap too, mainly for
 * host and CLI runs where app/config executes without compose startup hooks.
 */
if (!is_dir(filename: $tracyLogDir)) {
  @mkdir(directory: $tracyLogDir, permissions: 0775, recursive: true);
}

if (!is_writable(filename: $tracyLogDir)) {
  @chmod(filename: $tracyLogDir, permissions: 0777);
}

// Keep shared Tracy artifacts writable across container web, host and CLI users.
$normalizeTracyPermissions = static function (string $directory): void {
  if (!is_dir(filename: $directory)) {
    return;
  }

  @chmod(filename: $directory, permissions: 0777);

  foreach (glob(pattern: $directory . '*') ?: [] as $tracyPath) {
    @chmod(
      filename: $tracyPath,
      permissions: is_dir(filename: $tracyPath) ? 0777 : 0666,
    );
  }
};

$normalizeTracyPermissions(directory: $tracyLogDir);

register_shutdown_function(
  callback: static function () use ($tracyLogDir, $normalizeTracyPermissions): void {
    $normalizeTracyPermissions(directory: $tracyLogDir);
  },
);

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
