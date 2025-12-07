<?php

namespace Controllers;

abstract class BaseController
{
  // almacenar el nivel de acceso
  protected static $requiredRole = null;

  public function __construct()
  {
    // verificacion de carga de las funciones de seguridad
    if (!function_exists('isAuth')) {
      require_once __DIR__ . '/../includes/funciones.php';
    }

    // ejecutar la verificacion solo si se ha definido un rol requerido
    if (static::$requiredRole === 'admin') {

      // verifica ADMIN + autenticado (isAuth)
      isAdminOrRedirect();
    } else if (static::$requiredRole === 'auth') {

      // verifica solo autenticado
      isAuth();
    }
  }
}
