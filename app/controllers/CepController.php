<?php

namespace Controller;

use App\Services\ViaCepService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Quazymodo\AbstractController;
use Quazymodo\ComponentFactory;

class CepController extends AbstractController
{
  public function lookup(ServerRequestInterface $request): ResponseInterface
  {
    // Recupera o CEP enviado via POST
    $parsedBody = $request->getParsedBody();
    $cep = $parsedBody['cep'] ?? '';

    $viaCep = new ViaCepService();
    $data = $viaCep->getAddressByCep($cep);

    $fields = ComponentFactory::Template(
      "/pages/user/address/address-fields",
      [
        'address' => htmlspecialchars($data['logradouro'] ?? ''),
        'number' => '',
        'complement' => htmlspecialchars($data['complemento'] ?? ''),
        'district' => htmlspecialchars($data['bairro'] ?? ''),
        'city' => htmlspecialchars($data['localidade'] ?? ''),
        'uf' => htmlspecialchars($data['uf'] ?? ''),
      ]
    );

    return $this->html($fields);
  }
}
