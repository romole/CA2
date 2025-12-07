<?php

namespace Model;

class Usuario extends ActiveRecord
{
  protected static $tabla = 'usuarios';
  protected static $columnasDB = ['id', 'codigoRol', 'nombre', 'email', 'password', 'confirmadoTerminos', 'estadoCuenta', 'confirmadaCuenta', 'token'];

  public $id;
  public $codigoRol;
  public $nombre;
  public $email;
  public $password;
  public $passwordCheck; // no en BD - solo para validar
  public $confirmadoTerminos;
  public $estadoCuenta;
  public $confirmadaCuenta;
  public $token;

  public function __construct($args = [])
  {
    $this->id = $args['id'] ?? null;
    $this->codigoRol = $args['codigoRol'] ?? 22;

    $this->nombre = $args['nombre'] ?? '';
    $this->email = $args['email'] ?? '';
    $this->password = $args['password'] ?? '';
    $this->passwordCheck = $args['passwordCheck'] ?? ''; // para verificar

    $this->confirmadoTerminos = $args['confirmadoTerminos'] ?? 0;
    $this->estadoCuenta = $args['estadoCuenta'] ?? 2;
    $this->confirmadaCuenta = $args['confirmadaCuenta'] ?? 0;
    $this->token = $args['token'] ?? '';
  }


  /**
   * validarNuevaCuenta - CREAR cuenta
   * @return string[][] array Alertas de errores
   */
  public function validarNuevaCuenta()
  {
    if (!$this->nombre) {
      self::$alertas['error'][] = 'El nombre es obligatorio';
    }

    // parcial # validarEMAIL
    $this->validarEmail();

    // parcial # validarPASSWORD
    $this->validarPassword();

    if ($this->password !== $this->passwordCheck) {
      self::$alertas['error'][] = 'Las contraseñas no son iguales';
    }

    // parcial # validarCheckBox
    $this->validarCheckBox();
    if (!$this->confirmadoTerminos) {
      self::$alertas['error'][] = 'No se han aceptado los Términos, Condiciones y Privacidad';
    }

    return self::$alertas;
  }


  /**
   * existeUsuario
   * verifica si la cuenta/@email existe
   * @return mixed String de $alerta || Query
   */
  public function existeUsuario()
  {
    $query = "SELECT * FROM " . self::$tabla . " WHERE email='" . $this->email . "' LIMIT 1";
    $resultado = self::$db->query($query);

    if ($resultado->num_rows) {
      self::$alertas['error'][] = 'El usuario ya esta registrado';
    }
    return $resultado;
  }


  /**
   * validarLogin
   * alertas si no se ha proporcionado un valor de campo email y/o password
   * @return mixed String de $alerta || Query
   */
  public function validarLogin()
  {
    if (!$this->email) {
      self::$alertas['error'][] = 'El email es obligatorio';
    }
    if (!$this->password) {
      self::$alertas['error'][] = 'La password es obligatoria';
    }
    return self::$alertas;
  }

  public function comprobarPasswordAndVerificado($password)
  {
    $resultado = password_verify($password, $this->password);

    if (!$resultado || !$this->confirmadaCuenta) {
      self::$alertas['error'][] = 'Password incorrecta o la cuenta no esta confirmada';
    } else {
      return true;
    }
  }


  // PARCIALES
  //
  // parcial # validarEMAIL
  public function validarEmail()
  {
    if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
      self::$alertas['error'][] = 'El email no es valido';
    }
    return self::$alertas;
  }

  // parcial # validarPASSWORD
  public function validarPassword()
  {
    if (!$this->password) {
      self::$alertas['error'][] = 'La password es obligatoria';
      return;
    }

    # +7 caracteres y contendra minúsculas / mayúscula / números / símbolos (no alfanumérico)
    $regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{7,}$/';

    if (!preg_match($regex, $this->password)) {
      self::$alertas['error'][] = 'La password NO cumple los requisitos';
    }
    return self::$alertas;
  }

  // parcial # validarCheckBox
  public function validarCheckBox()
  {
    $checkTerminos   = !empty($_POST['c-terminos']);
    $checkPrivacidad = !empty($_POST['c-privacidad']);

    if ($checkTerminos && $checkPrivacidad) {
      $this->confirmadoTerminos = 1;
    }
  }

  // parcial # HASHpassword
  public function hashPassword()
  {
    $this->password = password_hash($this->password, PASSWORD_BCRYPT);
  }

  // parcial # crearTOKEN
  public function crearToken()
  {
    $this->token = uniqid();
  }
}
