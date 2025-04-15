<?php

namespace Quazymodo;

class Csrf
{
  static function setToken(): string 
{
  $_SESSION["csrf-token"] = bin2hex(random_bytes(16));
  return $_SESSION["csrf-token"];
}

static function verifyToken(string $token): bool 
{
  return isset($_SESSION['csrf-token']) && hash_equals($_SESSION['csrf-token'], $token);
}
}