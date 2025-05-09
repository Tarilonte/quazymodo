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
      int $statusCode
    ): ResponseInterface {
        $REQUEST_URI = $request->getServerParams()['REQUEST_URI'] ?? '';
        $component = ComponentFactory::create(
          'page-error',
          [
            'body' => ComponentFactory::loadTemplate('pages/error'),
            'requested-uri' => $REQUEST_URI,
            'page-title' => $this->getErrorMessage($statusCode)[1],
            'error-code' => $statusCode,
            'error-icon' => $this->getErrorMessage($statusCode)[0],
            'error-message' => $this->getErrorMessage($statusCode)[1],
            'error-description' => $this->getErrorMessage($statusCode, $REQUEST_URI)[2],
            'take-me-back' => $this->getErrorMessage($statusCode)[3],
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