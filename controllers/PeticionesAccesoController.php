<?php

namespace Controllers;

use MVC\Router;
use Classes\Session;
use Model\PeticionAcceso;
use Model\RegistroAprobacionAcceso;

class PeticionesAccesoController
{
  /**
   * obtencion de listado
   * @param Router $router
   * @return void
   */
  public function index(Router $router)
  {
    Session::requireAuth();

    $estado = isset($_GET['estado']) ? (int)$_GET['estado'] : null;

    $peticiones = PeticionAcceso::allWithRelations($estado);

    $router->render(
      'peticiones_acceso/index',
      compact('peticiones')
    );
  }

  /**
   * ver las peticiones existentes
   * @param Router $router
   * @return void
   */
  public function ver(Router $router)
  {
    Session::requireAuth();

    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

    /** @var PeticionAcceso $peticion */
    $peticion = PeticionAcceso::find($id);
    if (!$peticion) {
      header('Location: /peticiones-acceso');
      exit;
    }

    $router->render('peticiones_acceso/ver', compact('peticion'));
  }

  /**
   * decidir- aprobar / denegar
   * @return never
   */
  public function decidir()
  {
    Session::requireAuth();

    $accion     = $_POST['accion'] ?? null; // Aprobado | Denegado
    // $peticionId = (int)$_POST['id'];
    $peticionId = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
    if (!$peticionId || !in_array($accion, ['Aprobado', 'Denegado'], true)) {
      header('Location: /peticiones-acceso');
      exit;
    }
    $usuario = Session::user();
    $usuarioId = $usuario['id'];
    $rolUsuario = $usuario['rol'];


    /** @var \Model\PeticionAcceso $peticion - añadido para el linter */
    $peticion = PeticionAcceso::find($peticionId);
    if (
      !$peticion ||
      !$peticion->estaPendiente() ||
      RegistroAprobacionAcceso::usuarioYaDecidio($peticionId, $usuarioId)
    ) {
      header('Location: /peticiones-acceso');
      exit;
    }

    // validar rol de usuario para poder aprobar
    if (!$this->rolPuedeAprobar($peticion->id_tipo_acceso, $rolUsuario)) {
      header('Location: /acceso-denegado');
      exit;
    }

    $registro = new RegistroAprobacionAcceso([
      'id_peticion_acceso' => $peticionId,
      'id_usuario_aprobador' => $usuarioId,
      'accion' => $accion,
      'comentario' => $_POST['comentario'] ?? null
    ]);

    $registro->guardar();

    // validar automaticamente todos los estados (aprobado / denegado)
    $peticion->validarEstadoAutomaticamente();

    header('Location: /peticiones-acceso');
    exit;
  }

  /**
   * validar si el rol puede aprobar el tipo de acceso
   * @param integer $tipoAcceso
   * @param integer $rol
   * @return boolean
   */
  private function rolPuedeAprobar(int $tipoAcceso, int $rol): bool
  {
    $query = "
            SELECT 1 FROM peticion_con_rol
            WHERE id_tipo_acceso = {$tipoAcceso}
            AND id_rol_aprobador = {$rol}
            LIMIT 1
        ";
    return !empty(PeticionAcceso::consultarSQL($query));
  }
}
