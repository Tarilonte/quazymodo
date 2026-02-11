<?php

/*
 * App configuration entrypoint.
 *
 * This file is loaded by Quazymodo\App and aggregates domain
 * configuration files plus runtime bootstrap side effects.
 */

require_once __DIR__ . '/app.php';
require_once __DIR__ . '/rate-limit.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/services.php';
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/tracy.php';
