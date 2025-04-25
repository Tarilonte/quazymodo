<?php

namespace Controller;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Pusher\Pusher;
use Quazymodo\AbstractController;
use Quazymodo\ComponentFactory;

class ChatController extends AbstractController
{
    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $page = ComponentFactory::create("chatRoom");
        return $this->html($page);
    }

    public function broadcast(ServerRequestInterface $request): ResponseInterface
    {
        // Decodificando os dados da requisição
        $data = json_decode((string) $request->getBody(), true);

        // Configuraçao do GuzzleHttp Client
        // Desativando a verificação SSL para o cliente GuzzleHttp
        $custom_client = new Client([
            'verify' => false, // Desativa verificação SSL
        ]);
    
        // Configurações do Pusher
        $options = [
            'useTLS' => true,  
            'host' => 'quazymodo',
            'port' => 6001,
            'scheme' => 'https',
            'encrypted' => false,
        ];
    
        // Instanciando o Pusher
        $pusher = new Pusher(
            'app-key', // Substitua pela sua chave do app
            'app-secret', // Substitua pelo seu segredo do app
            'app-id', // Substitua pelo seu ID do app
            $options,
            $custom_client // Passando o cliente GuzzleHttp personalizado
        );
    
        // Dados do evento
        $eventData = [
            'name' => 'chat-message',
            'channel' => 'public-chat',
            'data' => ['message' => $data['message']]
        ];
    
        // Enviando o evento via Pusher
        try {
            $pusher->trigger($eventData['channel'], $eventData['name'], $eventData['data']);
            return $this->json(['status' => 'ok'], 200);
        } catch (\Pusher\PusherException $e) {
            // Em caso de erro
            return $this->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
