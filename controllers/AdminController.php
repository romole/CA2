<?php

namespace Controllers;

use MVC\Router;
use Model\Usuario; // Necesario para la gestión de roles

class AdminController
{
  /**
   * Dashboard principal del Admin
   * @param Router $router
   * @return void
   */
  public static function index(Router $router)
  {
    // Middleware de autenticación y administrador
    isAuth();
    isAdminOrRedirect();

    $router->render('admin/dashboard', [
      'titulo' => 'Panel de Administración',
    ]);
  }

  /**
   * Gestión de Roles de Usuarios (CRUD)
   * @param Router $router
   * @return void
   */
  public static function roles(Router $router)
  {
    isAuth();
    isAdminOrRedirect();

    $alertas = [];
    $usuarios = Usuario::all(); // obtener todos los usuarios

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $idUsuario = filter_var($_POST['id'], FILTER_VALIDATE_INT);
      $nuevoRol = s($_POST['rol']);

      if ($idUsuario && $nuevoRol) {
        // obtener el usuario por ID
        $usuario = Usuario::find($idUsuario);

        if ($usuario) {
          // Solo permitir el cambio si el nuevo rol es válido (ej: 1 o 22)
          if ($nuevoRol === '1' || $nuevoRol === '22') {
            $usuario->sincronizar(['codigoRol' => $nuevoRol]);
            $resultado = $usuario->guardar();

            if ($resultado) {
              Usuario::setAlerta('exito', "Rol del usuario actualizado correctamente.");
            } else {
              Usuario::setAlerta('error', "Hubo un error al actualizar el rol.");
            }
          } else {
            Usuario::setAlerta('error', "Rol no válido.");
          }
        } else {
          Usuario::setAlerta('error', "Usuario no encontrado.");
        }
      } else {
        Usuario::setAlerta('error', "Datos de rol inválidos.");
      }
      // Recargar para mostrar alertas y la lista actualizada
      $alertas = Usuario::getAlertas();
      $usuarios = Usuario::all();
    }

    $alertas = Usuario::getAlertas();

    $router->render('app/roles/index', [
      'titulo' => 'Gestión de Roles',
      'alertas' => $alertas,
      'usuarios' => $usuarios
    ]);
  }




  /**
   * Vistas de Gestión de Direcciones (Placeholder)
   * @param Router $router
   * @return void
   */
  public static function direcciones(Router $router)
  {
    isAuth();
    isAdminOrRedirect();

    $router->render('app/direcciones/index', [
      'titulo' => 'Gestión de Direcciones',
    ]);
  }

  /**
   * Vistas de Gestión de Salas (Placeholder)
   * @param Router $router
   * @return void
   */
  public static function salas(Router $router)
  {
    isAuth();
    isAdminOrRedirect();

    $router->render('app/salas/index', [
      'titulo' => 'Gestión de Salas',
    ]);
  }
}
