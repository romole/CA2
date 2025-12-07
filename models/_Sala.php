<?php

namespace Model;

class Sala extends ActiveRecord
{
  protected static $tabla = 'dir_salas';
  protected static $columnasDB = ['id', 'id_direccion', 'noSala', 'nombreSala'];

  public $id;
  public $id_direccion;
  public $noSala;
  public $nombreSala;


  public function __construct($args = [])
  {
    $this->id           = $args['id'] ?? null;
    $this->id_direccion = $args['id_direccion'] ?? '';
    $this->noSala       = $args['noSala'] ?? 0;
    $this->nombreSala   = $args['nombreSala'] ?? '';
  }

  public function validar()
  {
    if (!$this->id_direccion)
      self::$alertas['error'][] = 'Debe seleccionar una dirección';

    if (!$this->nombreSala)
      self::$alertas['error'][] = 'El nombre de la sala es obligatorio';

    return self::$alertas;
  }

  // relacion > Sala → Direccion
  public function direccion()
  {
    return Direccion::find($this->id_direccion);
  }
}
