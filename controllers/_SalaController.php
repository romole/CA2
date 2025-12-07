<?php

namespace Controllers;

use MVC\Router;
use Model\Sala;
use Model\Direccion;

class SalaController
{

  public static function index(Router $router)
  {
    self::adminOnly();

    $salas = Sala::all();

    $router->render("sala/index", [
      'salas' => $salas
    ]);
  }

  public static function crear(Router $router)
  {
    self::adminOnly();

    $sala = new Sala;
    $direcciones = Direccion::all();
    $alertas = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $sala->sincronizar($_POST);
      $alertas = $sala->validar();

      if (empty($alertas)) {
        $sala->guardar();
        header("Location: /sala");
        exit;
      }
    }

    $router->render("sala/crear", [
      'sala' => $sala,
      'direcciones' => $direcciones,
      'alertas' => $alertas
    ]);
  }

  public static function actualizar(Router $router)
  {
    self::adminOnly();

    $id = $_GET['id'] ?? null;
    $sala = Sala::find($id);
    $direcciones = Direccion::all();
    $alertas = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $sala->sincronizar($_POST);
      $alertas = $sala->validar();

      if (empty($alertas)) {
        $sala->guardar();
        header("Location: /sala");
        exit;
      }
    }

    $router->render("sala/actualizar", [
      'sala' => $sala,
      'direcciones' => $direcciones,
      'alertas' => $alertas
    ]);
  }

  public static function eliminar()
  {
    self::adminOnly();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $id = $_POST['id'];
      $sala = Sala::find($id);
      if ($sala) $sala->eliminar();
      header("Location: /sala");
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
