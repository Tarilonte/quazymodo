<?php
namespace Model;
use Medoo\Medoo;

class User
{
  private static Medoo $database;

  public static function init(): void
  {
    self::$database = setDatabase('USER');
  }

  public static function getUserBy(string $column, mixed $value): array|false
  {
    switch ($column) {
      case 'email':
        if (filter_var($value, FILTER_VALIDATE_EMAIL) == false) {
          return false;
        }
        break;
      
      default:
        # code...
        break;
    }
    $usuario = self::$database->get(
      'VIEW_user', // Tabela ou View
      '*',       // colunas desejadas
      [$column => $value] // clausula WHERE
    );
    if (self::$database->error) {
      die("Erro: getUserBy<br>". self::$database->error);
    }

    if (empty($usuario)) {
      return false;
    }

    return $usuario ?: false;
  }

  private static function createNewUser(array $data): false|array
  {
    $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
    unset($data['password']);
    $data['created_at'] = date('Y-m-d H:i:s');
    $data['login_cookie'] = bin2hex(random_bytes(16));

    self::$database->insert('user', $data);
    if (self::$database->error) die ("Erro: createNewUser (1)");
    $data['id'] = self::$database->id();
    setcookie('login_cookie', $data['login_cookie'], time() + (86400 * 30), "/");
    $data['email_confirm_token'] = self::set_token($data['id'], 'email_confirm_token');
    return $data;
  }

  private static function updateUser(int $user_id, array $data): array
  {
    self::$database->update(
      'user',
      $data,
      ['id' => $user_id]
    );
    if (self::$database->error) die ("Erro: updateUser");
    $updated_user = self::getUserBy('id', $user_id);
    return $updated_user;
  }

  public static function set_token(int $user_id, string $token_type): string
  {
    $token_value = bin2hex(random_bytes(16));
    self::$database->update(
      'user',
      [$token_type => $token_value],
      ['id' => $user_id]
    );
    if (self::$database->error) die ("Erro: set_token ($token_type)");
    return $token_value;
  }

  private static function setGravatarPicture(string $email): string
  {
    $hash_gravatar = md5( strtolower( trim( "$email" ) ) );
    $picture = "https://www.gravatar.com/avatar/$hash_gravatar.jpg?d=404";
    return $picture;
  }

  public static function registerFromGoogle(array $googlePayload): false|array
  {
    $data['email'] = $googlePayload['email'];
    $data['name'] = $googlePayload['given_name'];
    $data['picture_url'] = $googlePayload['picture'];
    $data['password'] = bin2hex(random_bytes(16));
    return self::createNewUser(data:$data);
  }

  public static function registerFromForm(array $form): false|array
  {
    $data['email'] = $form['email'];
    $data['name'] = $form['nome'];
    $data['picture_url'] = self::setGravatarPicture($form['email']);
    $data['password'] = $form['password'];
    return self::createNewUser(data:$data);
  }

  public static function login(array $usuario): void
  {
    $_SESSION['USER']['user-id'] = $usuario['id'];
    $_SESSION['USER']['user-email'] = $usuario['email'];
    $_SESSION['USER']['user-name'] = $usuario['name'];
    $_SESSION['USER']['user-picture'] = $usuario['picture_url'];
    if (!isset($_COOKIE['login_cookie']) || $_COOKIE['login_cookie']!= $usuario['login_cookie']) {
      setcookie('login_cookie', $usuario['login_cookie'], time() + (86400 * 30), "/");
    }
  }

  public static function logout(): void
  {
    session_unset();
    session_destroy();
    setcookie('login_cookie', '', -3600, "/");    
  }

  public static function verifyCredentials(string $email, string $password): array|false
  {
    $usuario = self::getUserBy('email',$email);
    if (!$usuario) {
      return false;
    }
    if (password_verify($password, $usuario['password_hash'])) {
      return $usuario;
    }
    return false;
  }

  public static function confirmEmail(int $user_id): array
  {
    $data = ['email_confirmed' => 'S'];
    return self::updateUser(user_id:$user_id, data:$data);
  }

  public static function resetPassword(int $user_id, string $password): array|false
  {
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    return self::updateUser(
      user_id:$user_id, 
      data:[
        'password_hash' => $password_hash,
        'password_reset_token' => ''
        ]
    );
  }
}

User::init();