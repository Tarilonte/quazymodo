<?php

namespace Controller;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Quazymodo\AbstractController;

/*
 * Local runtime switch endpoint.
 *
 * Intencao: alternar o APP_ENV persistente apenas em hosts locais, mantendo a
 * escrita limitada a declaracao esperada no arquivo de configuracao.
 */
class ChangeRuntimeController extends AbstractController
{
  private const LOCAL_HOSTS = [
    'localhost',
    '127.0.0.1',
    '::1',
    'quazymodo',
  ];

  public function toggle(): ResponseInterface
  {
    if (!$this->isLocalHost(host: $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '')) {
      return $this->json(
        body: [
          'message' => 'changeRuntime permitido apenas em host local.',
        ],
        status: 403,
      );
    }

    $configPath = dirname(path: __DIR__) . '/config/app.php';
    $source = file_get_contents(filename: $configPath);

    if ($source === false) {
      return $this->json(
        body: [
          'message' => 'Nao foi possivel ler app/config/app.php.',
        ],
        status: 500,
      );
    }

    $updatedSource = $this->toggleAppEnvironment(source: $source);

    if ($updatedSource === null) {
      return $this->json(
        body: [
          'message' => 'Declaracao valida de APP_ENV nao encontrada.',
        ],
        status: 500,
      );
    }

    if (file_put_contents(filename: $configPath, data: $updatedSource, flags: LOCK_EX) === false) {
      return $this->json(
        body: [
          'message' => 'Nao foi possivel gravar app/config/app.php.',
        ],
        status: 500,
      );
    }

    clearstatcache(clear_realpath_cache: true, filename: $configPath);

    if (function_exists(function: 'opcache_invalidate')) {
      opcache_invalidate(filename: $configPath, force: true);
    }

    sleep(1); // Pequena pausa para garantir que a escrita seja concluida antes do refresh.
    return new Response(
      status: 204,
      headers: [
        'HX-Refresh' => 'true',
      ],
    );
  }

  private function toggleAppEnvironment(string $source): ?string
  {
    $pattern = "/const\\s+APP_ENV\\s*=\\s*'(?<env>development|production)'\\s*;/";

    if (!preg_match(pattern: $pattern, subject: $source, matches: $matches)) {
      return null;
    }

    $nextEnvironment = $matches['env'] === 'development' ? 'production' : 'development';

    // Substitui somente a declaracao conhecida da constante de ambiente.
    return preg_replace(
      pattern: $pattern,
      replacement: "const APP_ENV = '{$nextEnvironment}';",
      subject: $source,
      limit: 1,
    );
  }

  private function isLocalHost(string $host): bool
  {
    $normalizedHost = $this->normalizeHost(host: $host);

    return in_array(needle: $normalizedHost, haystack: self::LOCAL_HOSTS, strict: true);
  }

  private function normalizeHost(string $host): string
  {
    $host = strtolower(string: trim(string: $host));

    if (str_starts_with(haystack: $host, needle: '[')) {
      $closingBracket = strpos(haystack: $host, needle: ']');

      if ($closingBracket !== false) {
        return substr(string: $host, offset: 1, length: $closingBracket - 1);
      }
    }

    if (substr_count(haystack: $host, needle: ':') === 1) {
      return explode(separator: ':', string: $host, limit: 2)[0];
    }

    return $host;
  }
}
