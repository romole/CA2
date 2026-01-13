<?php

namespace Model;

class ActiveRecord
{
  // base de datos
  protected static $db;
  protected static $tabla = '';
  protected static $columnasDB = [];

  // el ID del registro
  public $id;

  //  mensajes de alerta y validacion
  protected static $alertas = [];

  /**
   * conexion a la BD - mysqli
   * @param mixed $database
   * @return void
   */
  public static function setDB($database)
  {
    self::$db = $database;
  }

  //
  // === ALERTAS / VALIDACIONES ===

  /**
   * setter - mensaje de alerta
   * @param string $tipo Tipo de alerta ('error' 'success' ..)
   * @param string $mensaje Mensaje de alerta
   * @return void
   */
  public static function setAlerta($tipo, $mensaje)
  {
    static::$alertas[$tipo][] = $mensaje;
  }

  /**
   * getters - alertas registradas
   * @return array<string[]>
   */
  public static function getAlertas()
  {
    return static::$alertas;
  }

  /**
   * validacio - alertas - atributos
   * ..limpia alertas y devuelve un array vacio
   * @return array<string[]>
   */
  public function validar()
  {
    static::$alertas = [];
    return static::$alertas;
  }

  //
  // === CONSULTAS ===

  /**
   * consulta SQL + conversion a objetos
   * @param string $query Consulta SQL
   * @return array Array de objetos del modelo
   */
  public static function consultarSQL($query)
  {
    // consulta + verificación error
    $resultado = self::$db->query($query);
    if (!$resultado) return [];

    // iterar resultados a oo
    $array = [];
    while ($registro = $resultado->fetch_assoc()) {
      $array[] = static::crearObjeto($registro);
    }
    $resultado->free();

    return $array;
  }

  /**
   *crea objeto del modelo === al de la BD
   * @param array $registro Registro asociativo de la BD
   * @return static Objeto del modelo
   */
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

  //
  // === MANEJO DE ATRIBUTOS ===

  /**
   * devuelve union de atributos del modelo (columnas de la BD, sin ID)
   * @return array
   */
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

  /**
   * sanitizar atributos antes de guardar en la BD
   * @return array
   */
  public function sanitizarAtributos()
  {
    $atributos = $this->atributos();
    $sanitizado = [];

    foreach ($atributos as $key => $value) {
      $sanitizado[$key] = self::$db->escape_string($value);
    }

    return $sanitizado;
  }

  /**
   * sincroniza los atributos del objeto con un array de datos
   * @param array $args Datos a sincronizar
   * @return void
   */
  public function sincronizar($args = [])
  {
    foreach ($args as $key => $value) {
      if (property_exists($this, $key) && !is_null($value)) {
        $this->$key = $value;
      }
    }
  }

  //
  // === CRUD (CREATE / UPDATE / DELETE) ===

  /**
   * guarda registro actual
   * @return array|bool Resultado de la operacion
   */
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

  /**
   * crea un nuevo registro  en BD
   * @return array Resultado y nuevo ID
   */
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

    return [
      'resultado' => $resultado,
      'id' => self::$db->insert_id
    ];
  }

  /**
   * actualiza el registro existente
   * @return bool
   */
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

    // agregamos el ID al final en  clausula WHERE y de tipo 'i' (int)
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

  /**
   * elimina registro actual por ID de la BD
   * @return bool
   */
  public function eliminar()
  {
    // sanear y validar el ID
    $id = filter_var($this->id, FILTER_VALIDATE_INT);
    if (!$id) return false;

    // preparar y ejecutar
    $query = "DELETE FROM " . static::$tabla . " WHERE id = ? LIMIT 1";
    $stmt = self::$db->prepare($query);
    $stmt->bind_param('i', $id);
    $resultado = $stmt->execute();

    $stmt->close();

    return $resultado;
  }

  //
  // === CONSULTAS RAPIDAS ===

  /**
   * obtiene todos los registros
   * @return array
   */
  public static function all()
  {
    $query = "SELECT * FROM " . static::$tabla . " ORDER BY id DESC";
    $resultado = self::consultarSQL($query);
    return $resultado;
  }

  /**
   * obtener un registro por su ID
   * @param int $id
   * @return static|null
   */
  public static function find($id)
  {
    // sanear y validar el ID
    $id = filter_var($id, FILTER_VALIDATE_INT);
    if (!$id) return null;

    $query = "SELECT * FROM " . static::$tabla . " WHERE id = ?";

    // preparar y ejecutar
    $stmt = self::$db->prepare($query);
    $stmt->bind_param('i', $id); // 'i' === integer
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

  /**
   * obtener N registros
   * @param int $limite
   * @return array
   */
  public static function get($limite)
  {
    // sanear y validar el limite
    $limite = filter_var($limite, FILTER_VALIDATE_INT);
    if (!$limite) return [];

    $query = "SELECT * FROM " . static::$tabla . " ORDER BY id DESC LIMIT ?";

    // preparar y ejecutar
    $stmt = self::$db->prepare($query);
    $stmt->bind_param('i', $limite); // 'i' === integer
    $stmt->execute();
    $resultado = $stmt->get_result();

    // crear los objetos
    $array = [];
    while ($registro = $resultado->fetch_assoc()) {
      $array[] = static::crearObjeto($registro);
    }
    $stmt->close();

    return array_shift($array);  // devolver solo el primer objeto
    // return $array;
  }

  /**
   * busca registro por columna y valor
   * @param string $columna
   * @param mixed $valor
   * @return static|null
   */
  public static function where($columna, $valor)
  {
    // validacion de columna
    if (!in_array($columna, static::$columnasDB) || !is_string($valor)) {
      return null;
    }

    $query = "SELECT * FROM " . static::$tabla . " WHERE " . $columna . " = ?";
    // tipo de bind
    $tipo = is_int($valor) ? 'i' : 's'; // asumir como string 's'

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

  /**
   * busca todos los registros que coinciden con columna y valor
   * @param string $columna
   * @param mixed $valor
   * @return array
   */
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
}
