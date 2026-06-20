<?php

use Quazymodo\CSPManager;

/*
 * retuns a nonce attribute with valid nonce value for the current request.
 * Use it to insert the nonce attribute in your script tags, like this:
 * <script {{ nonce = plugin:/plugins/nonceAttributeCp/ }}>
 */

$cspNonce = (string) (CSPManager::getNonce() ?? '');

return [
  'template' => 'plugins/nonceAttributeCp/',
  'inserts' => [
    'csp-nonce' => $cspNonce,
  ],
];
