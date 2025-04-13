<?php

namespace Quazymodo;

class CSPManager
{
  private static $nonce;

  private static $directives = [
    'script-src' => ["'self'", "'unsafe-eval'"],
  ];

  public static function setNonce()
  {
    if (!isset(self::$nonce)) {
      self::$nonce = base64_encode(random_bytes(20));
      $_SESSION['csp-nonce'] = self::$nonce;
      self::addSource('script-src', "'nonce-" . self::getNonce() . "'");
    }
  }


  public static function getNonce()
  {
    if (!isset(self::$nonce)) {
      return $_SESSION['csp-nonce'];
    }
    return self::$nonce;
  }

  public static function addSource($directive, $source): void
  {
    // Verifica se a diretiva é suportada; caso contrário, ignora a adição
    if (!array_key_exists($directive, self::$directives)) {
      echo "Diretiva {$directive} não suportada.";
      return;
    }

    // Adiciona a origem à diretiva correspondente, evitando duplicatas
    if (!in_array($source, self::$directives[$directive])) {
      self::$directives[$directive][] = $source;
    }
  }

  public static function getDirectives(): null|string
  {
    $policies = [];
    foreach (self::$directives as $directive => $sources) {
      if (!empty($sources)) { // Verifica se há fontes definidas
        $policies[] = $directive . " " . implode(" ", $sources);
      }
    }
    if($_ENV['CSP_ENABLED']){
      return implode("; ", $policies);
    }
    return null;
  }
}