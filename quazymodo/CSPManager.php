<?php

namespace quazymodo;

class CSPManager
{
    private static $directives = [
        'script-src' => ["'self'"],
    ];

    public static function addSource($directive, $source)
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

    public static function sendCSPHeader()
    {
        $policies = [];
        foreach (self::$directives as $directive => $sources) {
            if (!empty($sources)) { // Verifica se há fontes definidas
                $policies[] = $directive . " " . implode(" ", $sources);
            }
        }
        
        // Constrói e envia o header CSP
        header("Content-Security-Policy: " . implode("; ", $policies));
    }
}