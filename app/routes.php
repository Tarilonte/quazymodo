<?php

/**
 *---------------------------------------
 * Padrões  de macth
 *---------------------------------------
 *
 * Documentação: https://dannyvankooten.github.io/AltoRouter/usage/mapping-routes.html
 *
 * *                    // Match all request URIs
 * [i]                  // Match an integer
 * [i:id]               // Match an integer as 'id'
 * [a:action]           // Match alphanumeric characters as 'action'
 * [h:key]              // Match hexadecimal characters as 'key'
 * [:action]            // Match anything up to the next / or end of the URI as 'action'
 * [create *edit:action] // Match either 'create' or 'edit' as 'action'
 * [*]                  // Catch all (lazy, stops at the next trailing slash)
 * [*:trailing]         // Catch all as 'trailing' (lazy)
 * [**:trailing]        // Catch all (possessive - will match the rest of the URI)
 * .[:format]?          // Match an optional parameter 'format' - a / or . before the block is also optional
 * 
*/

/**
* ---------------------------------------
*  Rotas cadastradas
* ---------------------------------------
* 
*  Aqui estão todas as rotas do site
*  $router->map( VERBO, rota, Controller, nome_da_rota );
*  ! as rotas devem ser declaradas em lower case
* 
*/

// Home
$router->map( 'GET', '/', 'home');

// Testes
$router->map( 'GET', '/modal', 'test/modal');
$router->map( 'GET', '/new', 'test/new');
$router->map( 'GET', '/raw', 'test/request-raw');