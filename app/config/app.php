<?php

/*
 * Core application identity and runtime mode.
 */
const APP_ENV = 'production'; // development, production
const APP_URL = 'https://localhost:8443';
const APP_NAME = 'Quazymodo';
const APP_TIMEZONE = 'America/Sao_Paulo';
const APP_LOCALE = 'pt_BR.utf8';
const APP_SESSION_ENABLE = 1; // 0 = disabled, 1 = enabled
const APP_CSP_ENABLED = 1; // CSP headers: 0 = disabled, 1 = enabled

// External frontend assets used by base components.
const ASSET_JQUERY = 'https://code.jquery.com/jquery-4.0.0.min.js';
const ASSET_ANIMATECSS = 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css';
const ASSET_HTMX = 'https://cdn.jsdelivr.net/npm/htmx.org@4.0.0-alpha6/dist/htmx.min.js';

// Configure process locale and timezone for date/format consistency.
date_default_timezone_set(APP_TIMEZONE);
setlocale(LC_ALL, APP_LOCALE);
