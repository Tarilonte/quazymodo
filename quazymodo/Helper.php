<?php

namespace Quazymodo;

use Psr\Http\Message\ServerRequestInterface;

class Helper
{
  /**
   * Retorna o IP do cliente a partir do ServerRequest.
   */
  public static function getClientIp(ServerRequestInterface $request): string
  {
    $serverParams = $request->getServerParams();

    $remoteAddr = (string) ($serverParams['REMOTE_ADDR'] ?? '');
    $trustedProxies = self::trustedProxies();

    if (
      $remoteAddr !== ''
      && in_array($remoteAddr, $trustedProxies, true)
      && !empty($serverParams['HTTP_X_FORWARDED_FOR'])
    ) {
      $forwarded = explode(',', (string) $serverParams['HTTP_X_FORWARDED_FOR']);
      $clientIp = trim($forwarded[0] ?? '');

      if (filter_var($clientIp, FILTER_VALIDATE_IP)) {
        return $clientIp;
      }
    }

    if (filter_var($remoteAddr, FILTER_VALIDATE_IP)) {
      return $remoteAddr;
    }

    return 'UNKNOWN';
  }

  private static function trustedProxies(): array
  {
    if (!defined('TRUSTED_PROXIES')) {
      return [];
    }

    $proxies = constant('TRUSTED_PROXIES');
    return is_array($proxies) ? $proxies : [];
  }

  /**
   * Busca recursivamente uma chave em um array multidimensional.
   */
  public static function recursiveArraySearch(array $array, string $keyToFind): ?string
  {
    foreach ($array as $key => $value) {
      if ($key === $keyToFind) {
        return is_array($value) ? print_r($value, true) : $value;
      } elseif (is_array($value)) {
        $result = self::recursiveArraySearch($value, $keyToFind);
        if ($result !== null) {
          return $result;
        }
      }
    }

    return null;
  }

  /**
   * 
   * Resolve Component Path
   * ----------------------
   * 
   * Resolve the path to a component file, ensuring it includes the correct file path.
   * 
   */
  public static function resolveComponentPath(string $componentName, string $componentType): string
  {
    if (substr($componentName, -1) === '/') {
      $componentName .= basename($componentName);
    }

    // If $componentType is 'page' and $componentName doesn't start with a slash, add /pages/ directory to the path
    if ($componentType === 'page' && !str_starts_with($componentName, '/')) {
      $componentName = '/pages/' . $componentName;
    }

    // If $componentType is 'plugin' and $componentName doesn't start with a slash, add /plugins/ directory to the path
    if ($componentType === 'plugin' && !str_starts_with($componentName, '/')) {
      $componentName = '/plugins/' . $componentName;
    }

    return $componentName;
  }
}
