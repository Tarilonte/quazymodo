<?php

namespace Entity;

use Psr\Http\Message\ServerRequestInterface;
use function Quazymodo\Functions\getClientIp;

class UserEntity
{
    public int $id;
    public string $name;
    public string $email;
    public string $ip;
    public array $role;

    public function __construct(ServerRequestInterface $request)
    {
      $this->ip = getClientIp($request);
      if (isset($_SESSION['user']['id'])) {
        $this->id = $_SESSION['user']['id'];
        $this->name = $_SESSION['user']['name'];
        $this->email = $_SESSION['user']['email'];
        $this->role = $_SESSION['user']['role'];        
      } else {
        $this->id = 0;
        $this->name = 'Visitante';
        $this->email = 'guest@example.com';
        $this->role = ['guest'];

        $_SESSION['user']['id'] = $this->id;
        $_SESSION['user']['name'] = $this->name;
        $_SESSION['user']['email'] = $this->email;
        $_SESSION['user']['ip'] = $this->ip;
        $_SESSION['user']['role'] = $this->role;
      }
    }
}
