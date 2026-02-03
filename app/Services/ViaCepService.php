<?php

namespace App\Services;

use Symfony\Component\HttpClient\HttpClient;

class ViaCepService
{
  public function getAddressByCep(string $cep): array
  {
    $cep = preg_replace('/\D/', '', $cep);
    if ($cep === '') {
      return [];
    }

    $client = HttpClient::create();
    $response = $client->request(
      'GET',
      "https://viacep.com.br/ws/{$cep}/json/"
    );

    return $response->toArray();
  }
}
