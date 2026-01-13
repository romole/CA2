<?php

namespace Model;

class PeticionAcceso extends ActiveRecord
{
  protected static $tabla = 'peticiones_acceso';

  protected static $columnasDB = [
    'id',
    'id_peticionario',
    'id_tipo_acceso',
    'id_sala',
    'fecha_creacion',
    'fecha_inicio',
    'fecha_final',
    'personal_accede',
    'motivo_acceso',
    'id_estado_acceso'
  ];

  public $id;
  public $id_peticionario;
  public $id_tipo_acceso;
  public $id_sala;
  public $fecha_creacion;
  public $fecha_inicio;
  public $fecha_final;
  public $personal_accede;
  public $motivo_acceso;
  public $id_estado_acceso;

  // CAMPOS JOIN (no BD)
  public $peticionario_nombre;
  public $tipo_acceso;
  public $estado;
  public $sala;
  public $direccion;

  public function __construct($args = [])
  {
    foreach (self::$columnasDB as $col) {
      $this->$col = $args[$col] ?? null;
    }

    $this->peticionario_nombre = $args['peticionario_nombre'] ?? null;
    $this->tipo_acceso         = $args['tipo_acceso'] ?? null;
    $this->estado              = $args['estado'] ?? null;
    $this->sala                = $args['sala'] ?? null;
    $this->direccion           = $args['direccion'] ?? null;
  }

  /**
   * devolver todas la peticiones - JOIN
   * @param mixed $estado
   * @return ActiveRecord[]
   */
  public static function allWithRelations(?int $estado = null): array
  {
    $query = "
            SELECT p.*,
                   u.nombre AS peticionario_nombre,
                   t.tipo   AS tipo_acceso,
                   e.estado AS estado,
                   s.nombreSala AS sala,
                   CONCAT(d.calle, ' ', d.num_calle, ', ', d.localidad) AS direccion
            FROM peticiones_acceso p
            JOIN usuarios u ON u.id = p.id_peticionario
            JOIN tipo_accesos t ON t.id = p.id_tipo_acceso
            JOIN estados_peticion e ON e.id = p.id_estado_acceso
            JOIN dir_salas s ON s.id = p.id_sala
            JOIN direcciones d ON d.id = s.id_direccion
        ";

    if ($estado !== null) {
      $query .= " WHERE p.id_estado_acceso = {$estado}";
    }

    $query .= " ORDER BY p.fecha_creacion DESC";

    return self::consultarSQL($query);
  }

  /**
   * saber si la petición está pendiente
   * @return bool
   */
  public function estaPendiente(): bool
  {
    return (int)$this->id_estado_acceso === 3;
  }

  /**
   * roles requeridos
   * @return int[]
   */
  public function rolesRequeridos(): array
  {
    $sql = "
            SELECT id_rol_aprobador
            FROM peticion_con_rol
            WHERE id_tipo_acceso = " . (int)$this->id_tipo_acceso;

    $result = self::consultarSQL($sql);
    return array_map(fn($r) => (int)$r->id_rol_aprobador, $result);
  }

  /**
   * roles que aprobaron
   * @return int[]
   */
  public function rolesQueAprobaron(): array
  {
    $sql = "
            SELECT DISTINCT u.codigoRol
            FROM registro_aprobaciones_accesos r
            JOIN usuarios u ON u.id = r.id_usuario_aprobador
            WHERE r.id_peticion_acceso = {$this->id}
              AND r.accion = 'Aprobado'
        ";

    $result = self::consultarSQL($sql);

    return array_map(fn($r) => (int)$r->codigoRol, $result);
  }

  /**
   * verificar denegacion
   * @return bool
   */
  public function tieneDenegacion(): bool
  {
    $sql = "
            SELECT 1
            FROM registro_aprobaciones_accesos
            WHERE id_peticion_acceso = {$this->id}
              AND accion = 'Denegado'
            LIMIT 1
        ";

    return !empty(self::consultarSQL($sql));
  }

  /**
   * validar aprobaciones multiples automaticamente
   * @return void
   */
  public function validarEstadoAutomaticamente(): void
  {
    // si hay un denegado → DENEGADA
    if ($this->tieneDenegacion()) {
      $this->id_estado_acceso = 2; // denegado
      $this->guardar();
      return;
    }

    // ¿todos los roles requeridos aprobaron?
    $rolesRequeridos = $this->rolesRequeridos();
    $rolesAprobados  = $this->rolesQueAprobaron();

    $faltan = array_diff($rolesRequeridos, $rolesAprobados);

    if (empty($faltan)) {
      $this->id_estado_acceso = 1; // aprobado
      $this->guardar();
    }
    // si faltan → esta PENDIENTE
  }
}
