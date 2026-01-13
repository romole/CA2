<?php

namespace Classes;

use Model\Usuario;

class Session
{
  private const TIMEOUT = 1800; // 30 minutos

  /**
   * verificar e inicia session
   * @return void
   */
  public static function start(): void
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
  }

  /**
   * crear session de usuario
   * @param Usuario $usuario
   * @return void
   */
  public static function login(Usuario $usuario): void
  {
    self::start();
    session_regenerate_id(true);

    $_SESSION = [
      'login' => true,
      'id'    => (int)$usuario->id,
      'nombre' => $usuario->nombre,
      'email' => $usuario->email,
      'rol'   => (int)$usuario->codigoRol,
      'last_activity' => time(),
    ];
  }

  /**
   * cerrar/destruye session de usuario + cookie
   * @return void
   */
  public static function logout(): void
  {
    self::start();
    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
      $params = session_get_cookie_params();
      setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
      );
    }

    session_destroy();
  }

  /**
   * verificar autenticado y expiracion por tiempo
   * @return bool
   */
  public static function check(): bool
  {
    self::start();

    if (empty($_SESSION['login'])) {
      return false;
    }

    // control de expiracion
    if (time() - ($_SESSION['last_activity'] ?? 0) > self::TIMEOUT) {
      self::logout();
      return false;
    }

    $_SESSION['last_activity'] = time();
    return true;
  }

  /**
   * getter de usuario autenticado
   * @return array{email: mixed, id: mixed, nombre: mixed, rol: mixed|null}
   */
  public static function user(): ?array
  {
    if (!self::check()) {
      return null;
    }

    return [
      'id'     => $_SESSION['id'],
      'nombre' => $_SESSION['nombre'],
      'email'  => $_SESSION['email'],
      'rol'    => $_SESSION['rol'],
    ];
  }

  // getter del ID ..del usuario
  public static function id(): ?int
  {
    return self::check() ? $_SESSION['id'] : null;
  }

  // getter del codigoRol ..del usuario
  public static function role(): ?int
  {
    return self::check() ? $_SESSION['rol'] : null;
  }

  // getter de verificar rol ADMIN(1)
  public static function isAdmin(): bool
  {
    return self::check() && $_SESSION['rol'] === 1;
  }

  // verifica si el usuario pertenece a uno de los roles permitidos
  public static function hasRole(array $roles): bool
  {
    return self::check() && in_array($_SESSION['rol'], $roles, true);
  }

  // obligar a que el usuario este logueado
  public static function requireAuth(string $redirect = '/'): void
  {
    if (!self::check()) {
      header("Location: $redirect");
      exit;
    }
  }

  // obligar a la autentificacion y rol valido
  public static function requireRole(array $roles, string $redirect = '/acceso-denegado'): void
  {
    self::requireAuth();

    if (!self::hasRole($roles)) {
      header("Location: $redirect");
      exit;
    }
  }
}
