<?php

namespace pangaMailer;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;
use pangaTemplater\Component;

class Mailer
{
  private $client;
  private $apiKey;

  public function __construct()
  {
    $this->client = new Client();
    $this->apiKey = $_ENV['MAILBABY_API_KEY'];
    return $this;
  }

  public function sendMail($from, $from_name, $to, $to_name, $subject, $body): string
  {
    $body = preg_replace('/\s+/', ' ', str_replace('"', '\'', $body));
    try {
      $response = $this->client->request('POST', 'https://api.mailbaby.net/mail/advsend', [
        'body' => json_encode([
          "subject" => $subject,
          "body"    => $body,
          "from"    => [
            "email" => $from,
            "name"  => $from_name,
          ],
          "to"      => [
              [
                "email" => $to,
                "name"  => $to_name,
              ],
          ],
        ]),
        'headers' => [
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
            'X-API-KEY'    => $this->apiKey,
        ],
        'timeout' => 5, // Define um tempo limite de 5 segundos
      ]);

      if ($response->getStatusCode() === 200) {
        // Sucesso na requisição
        return $response->getBody();
      } else {
        // Código de status diferente de 200
        return "Erro ao enviar e-mail. Código de status: " . $response->getStatusCode();
      }
    } catch (ConnectException $e) {
      // A requisição demorou mais do que o tempo limite
      return "A requisição excedeu o tempo limite.";
    } catch (RequestException $e) {
      // Erro na requisição
      if ($e->hasResponse()) {
        return "Erro ao enviar e-mail. Código de status: " . $e->getResponse()->getStatusCode();
      } else {
          return "Erro ao enviar e-mail. Nenhuma resposta recebida.";
      }
    } catch (\Exception $e) {
      // Outros erros
      return "Ocorreu um erro inesperado: " . $e->getMessage();
    }
  }

  public function send_newUser_email(array $usuario): string {
    $body = (new Component(
      componentName:"mailing/new-user",
        componentType:"simpleTemplate",
        controllerData:[
        "register-confirm-link" => $_ENV['BASE_URL'] . "/User/emailConfirm?". $usuario['email_confirm_token'],
        "user-name" => $usuario['name'],
        ]
    ))->render()->html;
    
    $email = $this->sendMail(
      from: 'noreply@mundofii.com',
      from_name: $_ENV['SITE_NAME'],
      to: $usuario['email'],
      to_name: $usuario['name'], 
      subject: "Bem-vindo ao mundofii, " . $usuario['name'], 
      body: $body
    );
    return $email;
  }

  public function send_resetPwd_token(array $usuario, $token): string {
    $body = (new Component(
      componentName:"mailing/reset-password",
        componentType:"simpleTemplate",
        controllerData:[
        "link" => $_ENV['BASE_URL'] . "/User/resetPassword?". $token
        ]
    ))->render()->html;
    
    $email = $this->sendMail(
      from: 'noreply@mundofii.com',
      from_name: $_ENV['SITE_NAME'],
      to: $usuario['email'],
      to_name: $usuario['email'], 
      subject: "Redefina a sua senha de acesso.", 
      body: $body
    );
    return $email;
  }

  public function send_redefinedPwd_msg(array $usuario): string {
    $body = (new Component(
      componentName:"mailing/password-redefined",
        componentType:"simpleTemplate",
        controllerData:[
        "user-name" => $usuario['name'],
        "link" => $_ENV['BASE_URL']
        ]
    ))->render()->html;
    
    $email = $this->sendMail(
      from: 'noreply@mundofii.com',
      from_name: $_ENV['SITE_NAME'],
      to: $usuario['email'],
      to_name: $usuario['email'], 
      subject: "Sua senha de acesso foi redefinida", 
      body: $body
    );
    return $email;
  }
}
