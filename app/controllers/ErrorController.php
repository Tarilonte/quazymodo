<?php
namespace Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Quazymodo\AbstractController;
use Quazymodo\ComponentFactory;

class ErrorController extends AbstractController
{
    public function handle(
      ServerRequestInterface $request,
      int $statusCode,
      array $meta = []
    ): ResponseInterface {
        $REQUEST_URI = $request->getServerParams()['REQUEST_URI'] ?? '';
        $errorData = $this->getErrorMessage($statusCode, $REQUEST_URI);

        $rateLimitInfo = '';
        if ($statusCode === 429) {
          $strikes = max(0, (int) ($meta['strikes'] ?? 0));
          $retryAfter = max(1, (int) ($meta['retry_after'] ?? 1));
          $remainingMinutes = max(1, (int) ceil($retryAfter / 60));

          $rateLimitInfo = componentFactory::Template('/pages/error/rate-limit-info', [
            'rate-limit-strikes' => $strikes,
            'rate-limit-remaining-minutes' => $remainingMinutes,
          ]);
        }

        $component = componentFactory::Page(
          '/pages/error/',
          [
            'page-title' => $errorData[1],
            'error-code' => $statusCode,
            'error-icon' => $errorData[0],
            'error-message' => $errorData[1],
            'error-description' => $errorData[2],
            'take-me-back' => $errorData[3],
            'rate-limit-info' => $rateLimitInfo,
            ]
      );
  

        return $this->html($component, $statusCode);
    }

    private function getErrorMessage(int $statusCode, string|null $REQUEST_URI = null): array
    {
        return match($statusCode) {
            400 => ['mdi mdi-robot-confused', 'Requisição inválida','',''],
            401 => ['ti ti-barrier-block', 'Acesso não autorizado','',''],
            403 => ['ti ti-barrier-block', 'Acesso proibido','',''],
            404 => ['ti ti-directions', 'Conteúdo não encontrado',"Não localizamos $REQUEST_URI",''],
            429 => ['mdi mdi-rabbit', 'Calma, veloz...','Você está fazendo muitas requisições.','hidden'],
            500 => ['mdi mdi-robot-confused', 'Desculpe, algo deu errado','Não conseguimos processar sua solicitação.',''],
            default => ['mdi mdi-robot-confused', 'Ocorreu um erro inesperado','','']
        };
    }


}
