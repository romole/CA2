<?php

namespace Model;

class Direccion extends ActiveRecord
{
  protected static $tabla = 'direcciones';
  protected static $columnasDB = ['id', 'calle', 'num_calle', 'localidad', 'cp', 'provincia', 'pais'];

  public $id;
  public $calle;
  public $num_calle;
  public $localidad;
  public $cp;
  public $provincia;
  public $pais;

  public function __construct($args = [])
  {
    $this->id = $args['id'] ?? null;
    $this->calle = $arg['calle'] ?? '';
    $this->num_calle = $arg['num_calle'] ?? '';
    $this->localidad = $arg['localidad'] ?? '';
    $this->cp = $arg['cp'] ?? '';
    $this->provincia = $arg['provincia'] ?? '';
    $this->pais = $arg['pais'] ?? 'España';
  }

  public function validar()
  {
    if (!$this->calle)      self::$alertas['error'][] = 'La calle es obligatoria';
    if (!$this->num_calle)  self::$alertas['error'][] = 'El número de calle es obligatorio';
    if (!$this->localidad)  self::$alertas['error'][] = 'La localidad es obligatoria';
    if (!$this->cp)         self::$alertas['error'][] = 'El código postal es obligatorio';
    if (!$this->provincia)  self::$alertas['error'][] = 'La provincia es obligatoria';
    if (!$this->pais)       self::$alertas['error'][] = 'El país es obligatorio';

    return self::$alertas;
  }
}
