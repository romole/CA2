<?php

namespace Model;

class RegistroAprobacionAcceso extends ActiveRecord
{
  protected static $tabla = 'registro_aprobaciones_accesos';

  protected static $columnasDB = [
    'id',
    'id_peticion_acceso',
    'id_usuario_aprobador',
    'accion',
    'comentario',
    'fecha_accion'
  ];

  public $id;
  public $id_peticion_acceso;
  public $id_usuario_aprobador;
  public $accion;
  public $comentario;
  public $fecha_accion;

  public function __construct($args = [])
  {
    foreach (self::$columnasDB as $col) {
      $this->$col = $args[$col] ?? null;
    }
  }

  /**
   * saber si un usuario ya aprobo la peticion
   * @param int $peticionId
   * @param int $usuarioId
   * @return bool
   */
  public static function usuarioYaDecidio(int $peticionId, int $usuarioId): bool
  {
    $query = "
            SELECT id FROM registro_aprobaciones_accesos
            WHERE id_peticion_acceso = {$peticionId}
            AND id_usuario_aprobador = {$usuarioId}
            LIMIT 1
        ";
    return !empty(self::consultarSQL($query));
  }
}
