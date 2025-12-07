<?php

namespace Model;

class ActiveRecord
{
  // base de datos
  protected static $db;
  protected static $tabla = '';
  protected static $columnasDB = [];

  // el ID explícitamente
  public $id;

  // alertas y mensajes
  protected static $alertas = [];

  // conexión a la BD
  public static function setDB($database)
  {
    self::$db = $database;
  }

  // setter - alerta
  public static function setAlerta($tipo, $mensaje)
  {
    static::$alertas[$tipo][] = $mensaje;
  }

  // getters - alertas
  public static function getAlertas()
  {
    return static::$alertas;
  }

  // validacion - alertas
  public function validar()
  {
    static::$alertas = [];
    return static::$alertas;
  }

  // consulta SQL para objeto en Memoria (Active Record)
  public static function consultarSQL($query)
  {
    // consulta + verificacion error
    $resultado = self::$db->query($query);
    if (!$resultado) return [];

    // iterar los resultados
    $array = [];
    while ($registro = $resultado->fetch_assoc()) {
      $array[] = static::crearObjeto($registro);
    }
    $resultado->free();

    return $array;
  }

  // crea objeto en memoria === al de la BD
  protected static function crearObjeto($registro)
  {
    $objeto = new static();

    foreach ($registro as $key => $value) {
      if (property_exists($objeto, $key)) {
        $objeto->$key = $value;
      }
    }
    return $objeto;
  }

  // identificar y unir los atributos de la BD
  public function atributos()
  {
    $atributos = [];
    foreach (static::$columnasDB as $columna) {
      if ($columna === 'id') {
        continue;   // por insercion/actualizacion
      }
      $atributos[$columna] = $this->$columna;
    }
    return $atributos;
  }

  // sanitizar datos antes de guardar en la BD
  public function sanitizarAtributos()
  {
    $atributos = $this->atributos();
    $sanitizado = [];

    foreach ($atributos as $key => $value) {
      $sanitizado[$key] = self::$db->escape_string($value);
    }

    return $sanitizado;
  }

  // sincroniza BD con Objetos en memoria
  public function sincronizar($args = [])
  {
    foreach ($args as $key => $value) {
      if (property_exists($this, $key) && !is_null($value)) {
        $this->$key = $value;
      }
    }
  }

  // registros - CRUD
  public function guardar()
  {
    $resultado = '';
    if (!is_null($this->id)) {
      // actualizar registro
      $resultado = $this->actualizar();
    } else {
      // crear nuevo registro
      $resultado = $this->crear();
    }
    return $resultado;
  }

  // obtener TODOS los registros
  public static function all()
  {
    $query = "SELECT * FROM " . static::$tabla . " ORDER BY id DESC";
    $resultado = self::consultarSQL($query);
    return $resultado;
  }

  // obtener un registro Por su ID
  public static function find($id)
  {
    // sanear y validar el ID
    $id = filter_var($id, FILTER_VALIDATE_INT);
    if (!$id) return null;

    $query = "SELECT * FROM " . static::$tabla . " WHERE id = ?";

    // preparar y ejecutar
    $stmt = self::$db->prepare($query);
    $stmt->bind_param('i', $id); // 'i' para integer
    $stmt->execute();
    $resultado = $stmt->get_result();

    // crear el objeto
    $registro = $resultado->fetch_assoc();
    $stmt->close();

    if ($registro) {
      $array = [static::crearObjeto($registro)];

      return array_shift($array);
    }

    return null;
  }

  // obtener N registros
  public static function get($limite)
  {
    // sanear y validar el limite
    $limite = filter_var($limite, FILTER_VALIDATE_INT);
    if (!$limite) return [];

    $query = "SELECT * FROM " . static::$tabla . " ORDER BY id DESC LIMIT ?";

    // preparar y ejecutar
    $stmt = self::$db->prepare($query);
    $stmt->bind_param('i', $limite); // 'i' para integer
    $stmt->execute();
    $resultado = $stmt->get_result();

    // crear los objetos
    $array = [];
    while ($registro = $resultado->fetch_assoc()) {
      $array[] = static::crearObjeto($registro);
    }
    $stmt->close();

    return array_shift($array);
  }

  // buscar $valor en la $columna
  public static function where($columna, $valor)
  {
    // validacion de columna
    if (!in_array($columna, static::$columnasDB) || !is_string($valor)) {
      return null;
    }

    $query = "SELECT * FROM " . static::$tabla . " WHERE " . $columna . " = ?";

    // tipo de bind
    $tipo = is_int($valor) ? 'i' : 's'; // asumir 's' string si no es int

    // preparar y ejecutar
    $stmt = self::$db->prepare($query);
    $stmt->bind_param($tipo, $valor);
    $stmt->execute();
    $resultado = $stmt->get_result();

    // crear objeto
    $registro = $resultado->fetch_assoc();
    $stmt->close();

    if ($registro) {
      $array = [static::crearObjeto($registro)];
      return array_shift($array);
    }

    return null;
  }


  // buscar TODOS los registros que coinciden con $valor en $columna -
  public static function whereAll(string $columna, $valor): array
  {
    // validacion de columna
    if (!in_array($columna, static::$columnasDB)) {
      return [];
    }

    $query = "SELECT * FROM " . static::$tabla . " WHERE " . $columna . " = ?";

    // tipo de bind (asumir 's' si no es int)
    $tipo = is_int($valor) ? 'i' : 's';

    // preparar y ejecutar
    $stmt = self::$db->prepare($query);
    $stmt->bind_param($tipo, $valor);
    $stmt->execute();
    $resultado = $stmt->get_result();

    // crear objeto
    $array = [];
    while ($registro = $resultado->fetch_assoc()) {
      $array[] = static::crearObjeto($registro);
    }
    $stmt->close();

    return $array;
  }


  // crea un nuevo registro
  public function crear()
  {
    $atributos = $this->sanitizarAtributos();
    $columnas = array_keys($atributos);
    $valores = array_values($atributos);

    // placeholders '?'
    $placeholders = implode(', ', array_fill(0, count($columnas), '?'));
    $tipos = str_repeat('s', count($columnas)); // se asume que todos son string 's'

    // consulta preparada
    $query = "INSERT INTO " . static::$tabla . " (" . implode(', ', $columnas) . ") VALUES (" . $placeholders . ")";

    $stmt = self::$db->prepare($query);

    // enlazar parametros
    $params = array_merge([$tipos], $valores);
    $refs = [];
    foreach ($params as $key => $value) {
      $refs[$key] = &$params[$key];
    }
    call_user_func_array([$stmt, 'bind_param'], $refs);

    // ejecutar
    $resultado = $stmt->execute();

    // retorno
    return [
      'resultado' => $resultado,
      'id' => self::$db->insert_id
    ];
  }

  // actualizar el registro
  public function actualizar()
  {
    $atributos = $this->sanitizarAtributos();
    $valores_set = [];
    $valores = [];
    $tipos = '';

    foreach ($atributos as $key => $value) {
      $valores_set[] = "{$key} = ?";
      $valores[] = $value;
      $tipos .= 's'; // se asume string
    }

    // agregamos el ID al final para la clausula WHERE y su tipo 'i' (int)
    $valores[] = $this->id;
    $tipos .= 'i';

    // consulta preparada
    $query = "UPDATE " . static::$tabla . " SET " . implode(', ', $valores_set);
    $query .= " WHERE id = ? LIMIT 1";

    $stmt = self::$db->prepare($query);

    // enlazar parametros
    $params = array_merge([$tipos], $valores);
    $refs = [];
    foreach ($params as $key => $value) {
      $refs[$key] = &$params[$key];
    }
    call_user_func_array([$stmt, 'bind_param'], $refs);

    // ejecutar
    $resultado = $stmt->execute();

    return $resultado;
  }

  // eliminar un registro por su ID
  public function eliminar()
  {
    // sanear y validar el ID
    $id = filter_var($this->id, FILTER_VALIDATE_INT);
    if (!$id) return false;

    $query = "DELETE FROM " . static::$tabla . " WHERE id = ? LIMIT 1";

    // preparar y ejecutar
    $stmt = self::$db->prepare($query);
    $stmt->bind_param('i', $id);
    $resultado = $stmt->execute();

    $stmt->close();

    return $resultado;
  }
}
