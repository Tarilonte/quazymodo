<?php

/*
 * Global defaults for request rate limiting.
 */
const RATE_LIMIT_REQUESTS = 0; // default requests per period
const RATE_LIMIT_PERIOD = 60; // default period in seconds
const RATE_LIMIT_DB_PATH = __DIR__ . '/../writable/db/rate_limit.sqlite';
