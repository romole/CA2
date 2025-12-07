<?php

namespace Controllers;

use MVC\Router;
use Model\Direccion;

class DireccionController
{

  public static function index(Router $router)
  {
    self::adminOnly();

    $direcciones = Direccion::all();

    $router->render("direccion/index", [
      'direcciones' => $direcciones
    ]);
  }

  public static function crear(Router $router)
  {
    self::adminOnly();

    $direccion = new Direccion;
    $alertas = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $direccion->sincronizar($_POST);
      $alertas = $direccion->validar();

      if (empty($alertas)) {
        $direccion->guardar();
        header('Location: /direccion');
        exit;
      }
    }

    $router->render("direccion/crear", [
      'direccion' => $direccion,
      'alertas' => $alertas
    ]);
  }

  public static function actualizar(Router $router)
  {
    self::adminOnly();

    $id = $_GET['id'] ?? null;
    $direccion = Direccion::find($id);
    $alertas = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $direccion->sincronizar($_POST);
      $alertas = $direccion->validar();

      if (empty($alertas)) {
        $direccion->guardar();
        header('Location: /direccion');
        exit;
      }
    }

    $router->render("direccion/actualizar", [
      'direccion' => $direccion,
      'alertas' => $alertas
    ]);
  }

  public static function eliminar()
  {
    self::adminOnly();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $id = $_POST['id'];
      $direccion = Direccion::find($id);
      if ($direccion) $direccion->eliminar();
      header('Location: /direccion');
    }
  }

  private static function adminOnly()
  {
    if (!isset($_SESSION['login']) || $_SESSION['login'] != 0) {
      header("Location: /");
      exit;
    }
  }
}
