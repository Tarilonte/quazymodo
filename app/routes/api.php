<?php

/*
 * API routes for JSON and async endpoints.
 */

// CEP lookup endpoint.
$router->map(method: 'POST', path: '/api/cep/lookup', handler: 'Controller\CepController::lookup');
