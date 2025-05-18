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

    $ipHeaders = [
      'HTTP_CLIENT_IP',
      'HTTP_X_FORWARDED_FOR',
      'REMOTE_ADDR',
    ];

    foreach ($ipHeaders as $header) {
      if (!empty($serverParams[$header])) {
        return $serverParams[$header];
      }
    }

    return 'UNKNOWN';
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
