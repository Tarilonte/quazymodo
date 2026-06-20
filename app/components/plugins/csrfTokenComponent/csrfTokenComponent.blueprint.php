<?php

use Quazymodo\Csrf;

/*
 * Reuse current session token when available, so multiple forms on same page
 * stay aligned with middleware validation.
 */
$csrfToken = $_SESSION['csrf-token'] ?? Csrf::setToken();

return [
  'template' => 'plugins/csrfTokenComponent/csrfTokenComponent',
  'inserts' => [
    'csrf-token' => $csrfToken,
  ],
];
