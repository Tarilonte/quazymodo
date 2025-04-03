<?php

namespace Quazymodo;

use Psr\Http\Message\ResponseInterface;
use Nyholm\Psr7\Response;
use Quazymodo\BaseComponent;

abstract class AbstractController 
{
    // Método para HTML (aplica CSP)
    protected function html(BaseComponent $component, int $status = 200): ResponseInterface 
    {
        $html = $component->render();
        $cspHeader = $component->getCspHeader(); // Obtém diretivas CSP do componente
        
        return $this->httpResponse(
            $html,
            $status,
            'text/html',
            $cspHeader ? ['Content-Security-Policy' => $cspHeader] : []
        );
    }

    // Método para JSON (sem CSP)
    protected function json(array $body, int $status = 200): ResponseInterface 
    {
        return $this->httpResponse(
            json_encode($body),
            $status,
            'application/json'
        );
    }

    // Método base (genérico)
    protected function httpResponse(
        string $body,
        int $status = 200,
        string $contentType = 'text/plain',
        array $customHeaders = []
    ): ResponseInterface 
    {
        $response = new Response($status);
        $response->getBody()->write($body);

        // Headers padrão (comuns a todos os tipos)
        $defaultHeaders = [
            'Content-Type' => $contentType,
            'X-Content-Type-Options' => 'nosniff'
        ];

        // Adiciona CSP apenas se existir (para HTML)
        $headers = array_merge($defaultHeaders, $customHeaders);

        foreach ($headers as $name => $value) {
            $response = $response->withHeader($name, $value);
        }

        return $response;
    }
}