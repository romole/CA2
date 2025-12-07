<?php

namespace Model;

class Acceso extends ActiveRecord
{
  protected static $tabla = 'peticiones_acceso';
  protected static $columnasDB = [
    'id',
    'id_peticionario',
    'id_sala',
    'id_tipo_acceso',
    'id_estado_acceso',
    'fecha_creacion',
    'fecha_inicio',
    'fecha_final',
    'personal_accede',
    'motivo_acceso'
  ];

  public $id;
  public $id_peticionario;
  public $id_sala;
  public $id_tipo_acceso;
  public $id_estado_acceso;
  public $fecha_creacion;
  public $fecha_inicio;
  public $fecha_final;
  public $personal_accede;
  public $motivo_acceso;

  // Campos JOIN para mostrar información en la vista
  public $nombreSala;
  public $tipoAcceso;
  public $estadoAcceso;
  public $nombrePeticionario;

  public function __construct($args = [])
  {
    $this->id = $args['id'] ?? null;
    $this->id_peticionario = $args['id_peticionario'] ?? '';
    $this->id_sala = $args['id_sala'] ?? '';
    $this->id_tipo_acceso = $args['id_tipo_acceso'] ?? '';
    $this->id_estado_acceso = $args['id_estado_acceso'] ?? 3; // default pendiente
    $this->fecha_creacion = $args['fecha_creacion'] ?? date('Y-m-d H:i:s');
    $this->fecha_inicio = $args['fecha_inicio'] ?? '';
    $this->fecha_final = $args['fecha_final'] ?? '';
    $this->personal_accede = $args['personal_accede'] ?? '';
    $this->motivo_acceso = $args['motivo_acceso'] ?? '';

    // Campos JOIN
    $this->nombreSala = $args['nombreSala'] ?? '';
    $this->tipoAcceso = $args['tipoAcceso'] ?? '';
    $this->estadoAcceso = $args['estadoAcceso'] ?? '';
    $this->nombrePeticionario = $args['nombrePeticionario'] ?? '';
  }

  /**
   * Validación de nueva petición
   */
  public function validarNuevaPeticion()
  {
    if (!$this->id_sala) self::$alertas['error'][] = 'Debes seleccionar una Sala.';
    if (!$this->id_tipo_acceso) self::$alertas['error'][] = 'Debes seleccionar un Tipo de Acceso.';
    if (!$this->fecha_inicio || !$this->fecha_final) self::$alertas['error'][] = 'Debes indicar las fechas de inicio y fin.';
    if (!$this->motivo_acceso || strlen($this->motivo_acceso) < 10) self::$alertas['error'][] = 'El motivo de acceso es obligatorio y debe tener al menos 10 caracteres.';

    return self::$alertas;
  }

  /**
   * Obtener todas las peticiones con JOINs correctos
   */
  public static function getPeticionesConInfo()
  {
    $query = "SELECT
                    p.id, p.id_peticionario, p.id_sala, p.id_tipo_acceso, p.id_estado_acceso,
                    p.fecha_creacion, p.fecha_inicio, p.fecha_final, p.personal_accede, p.motivo_acceso,
                    ds.nombreSala,
                    ta.tipo AS tipoAcceso,
                    ep.estado AS estadoAcceso,
                    u.nombre AS nombrePeticionario
                  FROM " . static::$tabla . " p
                  INNER JOIN dir_salas ds ON p.id_sala = ds.id
                  INNER JOIN tipo_accesos ta ON p.id_tipo_acceso = ta.id
                  INNER JOIN estados_peticion ep ON p.id_estado_acceso = ep.id
                  INNER JOIN usuarios u ON p.id_peticionario = u.id
                  ORDER BY p.fecha_final ASC";

    return self::consultarSQL($query);
  }

  /**
   * Obtener todas las salas
   */
  public static function getSalas()
  {
    $query = "SELECT id, nombreSala FROM dir_salas ORDER BY nombreSala ASC";
    $resultado = self::$db->query($query);
    $salas = [];
    while ($registro = $resultado->fetch_assoc()) $salas[] = $registro;
    $resultado->free();
    return $salas;
  }

  /**
   * Obtener todos los tipos de acceso
   */
  public static function getTiposAcceso()
  {
    $query = "SELECT id, tipo FROM tipo_accesos ORDER BY tipo ASC";
    $resultado = self::$db->query($query);
    $tipos = [];
    while ($registro = $resultado->fetch_assoc()) $tipos[] = $registro;
    $resultado->free();
    return $tipos;
  }
}
