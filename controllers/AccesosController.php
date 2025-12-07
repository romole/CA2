<?php

namespace Controllers;

use Model\Acceso;
use MVC\Router;

class AccesosController extends BaseController
{
  // solo autenticacion
  protected static $requiredRole = 'auth';


  /**
   * ver todos los registros de peticiones_acceso en una tabla
   * @param Router $router
   * @return void
   */
  public static function index(Router $router)
  {
    // obtener todas las peticiones
    $peticiones = Acceso::getPeticionesConInfo(); // prueba

    $router->render('accesos/index', [
      'titulo' => 'Listado de Peticiones de Acceso',
      'peticiones' => $peticiones
    ]);
  }





  /**
   * formulario y realiza peticion sobre peticiones_acceso
   * @param Router $router
   * @return void
   */
  public static function crearPeticion(Router $router)
  {
    $alertas = [];
    $peticion = new Acceso();

    // $salas = Acceso::getSalas();
    // $tipos_acceso = Acceso::getTiposAcceso();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $peticion->sincronizar($_POST);

      if (!isset($_SESSION)) {
        session_start();
      }

      // ID del usuario actual
      $peticion->id_peticionario = $_SESSION['id'];

      $alertas = $peticion->validarNuevaPeticion();

      if (empty($alertas)) {
        // guardar en la BD
        // estado peticion > 3 = pendiente
        $resultado = $peticion->guardar();

        if ($resultado['resultado']) {
          header('Location: /accesos');
          return;
        } else {
          Acceso::setAlerta('error', 'Hubo un error al guardar la petición.');
        }
      }
    }

    $alertas = Acceso::getAlertas();

    // 4. Renderizar la vista
    $router->render('accesos/crear', [
      'titulo' => 'Nueva Petición de Acceso',
      'alertas' => $alertas,
      // 'peticion' => $peticion,
      // 'salas' => $salas,
      // 'tipos_acceso' => $tipos_acceso
    ]);
  }
}
