<?php

namespace App\Entities;

use Quazymodo\BaseRepository;

class UserEntity
{
  protected ?string $dbHost = 'default';
  protected ?string $dbName = 'user';
  protected ?string $dbTable = 'user';
  protected BaseRepository $Repository;

  public function __construct()
  {
    $this->Repository = new BaseRepository($this->dbHost, $this->dbName, $this->dbTable);
  }

  public function getSessionInfo()
  {    
    if (!isset($_SESSION['user'])) {
    self::setSessionInfo();
    }
    return $_SESSION['user'];
  }

  public function setSessionInfo(?array $userInfo=null): void
  {  
    if (empty($userInfo)) {
    $_SESSION['user']['roles'] = 'GUEST';
    } else {
    $_SESSION['user'] = [...$userInfo];
    }
  }

  public function resetSessionInfo(): void
  {
    unset($_SESSION['user']);
  }

  public function get($id)
  {
    return $this->Repository->useTable('view_user')->findById($id);
  }

  public function getRoles($id)
  {
    $roles = $this->Repository->useTable('role')->findAll(['user_id' => $id]);
    return array_column($roles, 'role');
  }
}
