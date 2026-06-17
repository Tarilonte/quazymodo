<?php

namespace Controller;

use Psr\Http\Message\ResponseInterface;
use Quazymodo\AbstractController;
use Quazymodo\Csrf;

/*
 * Environment switch endpoint.
 *
 * Intencao: alterar apenas o override temporario da sessao atual e deixar o
 * bootstrap aplicar o novo ambiente na proxima requisicao.
 */
class EnvironmentController extends AbstractController
{
  public function update(): ResponseInterface
  {
    $token = is_string($_POST['csrf-token'] ?? null) ? $_POST['csrf-token'] : '';

    if (!Csrf::verifyToken(token: $token)) {
      return $this->json(
        body: [
          'message' => 'Token CSRF invalido.',
        ],
        status: 403,
      );
    }

    $environment = is_string($_POST['environment'] ?? null) ? $_POST['environment'] : '';
    $allowedEnvironments = ['development', 'production'];

    if (!in_array(needle: $environment, haystack: $allowedEnvironments, strict: true)) {
      return $this->json(
        body: [
          'message' => 'Ambiente invalido.',
        ],
        status: 422,
      );
    }

    // A sessao guarda somente o override efemero; nada e persistido em arquivo ou banco.
    $_SESSION['app-environment'] = $environment;

    $response = $this->json(
      body: [
        'message' => "Ambiente alterado para {$environment}.",
        'environment' => $environment,
      ],
    );

    // Nyholm PSR-7 nao expoe nomes de parametros estaveis para named arguments.
    return $response->withHeader('HX-Refresh', 'true');
  }
}
