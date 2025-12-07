<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController
{
  /**
   * CREAR cuenta
   * @param Router $router
   * @return void
   */
  public function crear(Router $router)
  {
    $usuario = new Usuario($_POST);

    // inicializar antes de validar
    $alertas = [];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

      $usuario->sincronizar($_POST);    // sincronizar Object con $_POST
      $alertas = $usuario->validarNuevaCuenta();

      // alertas === null => comprobar email
      if (empty($alertas)) {
        $resultado = $usuario->existeUsuario();

        if ($resultado->num_rows) {
          $alertas = Usuario::getAlertas(); // error
        } else {
          // 1 hashea password + crea token
          $usuario->hashPassword();
          $usuario->crearToken();

          // 2 alta de usuario (Token y datos)
          $resultado = $usuario->guardar();

          if ($resultado) {
            // 3 envio de Email
            // TO
            $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
            // FROM - enviar email de confirmacion
            $fromEmail = $_ENV['EMAIL_APP_DOMAIN'] ?: 'test.acount@test.test'; // test credentials - email address
            $fromName = $_ENV['EMAIL_APP_NAME'] ?: 'ca2.test.test'; // test credentials - username in email
            $email->enviarConfirmacion($fromEmail, $fromName);

            // 4 flag de registro + redireccion
            header('Location: /alta-mensaje');
          }
        }
      }
    }

    $router->render('auth/crear', [
      'usuario' => $usuario,
      'alertas' => $alertas
    ]);
  }

  // render mensaje de alta de cuenta
  public function altaMensaje(Router $router)
  {
    $router->render('auth/alta-mensaje');
  }

  /**
   * altaCONFIRMAR cuenta por token
   * @param Router $router
   * @return void
   */
  public function altaConfirmar(Router $router)
  {
    $alertas = [];
    $token = s($_GET['token']);

    $usuario = Usuario::where('token', $token);

    if (empty($usuario)) {
      Usuario::setAlerta('error', 'Token no valido');
    } else {
      $usuario->confirmadaCuenta = '1';
      $usuario->token = null;
      $usuario->guardar();
      Usuario::setAlerta('exito', 'Cuenta comprobada correctamente');
    }

    $alertas = Usuario::getAlertas();
    $router->render('auth/alta-confirmar', [
      'alertas' => $alertas
    ]);
  }



  /**
   * LOGIN session
   * @param Router $router
   * @return void
   */
  public function login(Router $router)
  {
    $alertas = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $auth = new Usuario($_POST);
      $alertas = $auth->validarLogin();

      // verificar usuario > password >..> confirmar
      if (empty($alertas)) {

        // la siguente linea @var corrige/silencia el error del linter para que mire en la clase hija Usuario en vez del padre ActiveRecord
        /** @var \Model\Usuario $usuario */
        $usuario = Usuario::where('email', $auth->email);

        if ($usuario) {    //existe email?
          if ($usuario->comprobarPasswordAndVerificado($auth->password)) {

            $_SESSION['id'] = $usuario->id;
            $_SESSION['nombre'] = $usuario->nombre;
            $_SESSION['emai'] = $usuario->email;
            $_SESSION['login'] = true;

            if ($usuario->codigoRol === '1') { // usuario ADMIN ??
              $_SESSION['admin'] = $usuario->codigoRol ?? null;
              header('Location: /admin');
            } else {
              header('Location: /accesos');
            }
          }
        } else {
          Usuario::setAlerta('error', 'Usuario no encontrado');
        }
      }
    }

    $alertas = Usuario::getAlertas();
    $router->render('auth/login', [
      'alertas' => $alertas,
    ]);
  }



  /**
   * OLVIDE cuenta.. reset password
   * @param Router $router
   * @return void
   */
  public function olvide(Router $router)
  {
    $alertas = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $auth = new Usuario($_POST);

      $alertas = $auth->validarEmail();

      if (empty($alertas)) {

        // la siguente linea @var corrige/silencia el error del linter para que mire en la clase hija Usuario en vez del padre ActiveRecord
        /** @var \Model\Usuario $usuario */
        $usuario = Usuario::where('email', $auth->email);

        if ($usuario && $usuario->confirmadaCuenta === 1) {

          // generar nuevo token + salvar
          $usuario->crearToken();
          $usuario->guardar();

          // envio de Email
          // TO
          $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
          // FROM - enviar email de confirmacion
          $fromEmail = $_ENV['EMAIL_APP_DOMAIN'] ?: 'test.acount@test.test'; // test credentials - email address
          $fromName = $_ENV['EMAIL_APP_NAME'] ?: 'ca2.test.test'; // test credentials - username in email
          $email->enviarInstrucciones($fromEmail, $fromName);

          Usuario::setAlerta('exito', 'Revisa tu email');
        } else {
          Usuario::setAlerta('error', 'La cuenta no existe o no esta confirmada');
        }
      }
    }

    $alertas = Usuario::getAlertas();
    $router->render('auth/olvido', [
      'alertas' => $alertas
    ]);
  }

  /**
   * RECUPERAR cuenta.. validacion password
   * @param Router $router
   * @return void
   */
  public function recuperar(Router $router)
  {
    $alertas = [];
    $error = false;

    $token = s($_GET['token']);

    // la siguente linea @var corrige/silencia el error del linter para que mire en la clase hija Usuario en vez del padre ActiveRecord
    /** @var \Model\Usuario $usuario */
    $usuario = Usuario::where('token', $token);
    // debuguear($usuario->email);

    if (empty($usuario)) {
      Usuario::setAlerta('error', 'Token no valido');
      $error = true;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $password = new Usuario($_POST);

      $alertas = $password->validarPassword();

      // asignacion + hash - token >..> Objeto >..> guardar()
      if (empty($alertas)) {
        $usuario->password = $password->password;
        $usuario->hashPassword();
        $usuario->token = null;

        $resultado = $usuario->guardar();
        if ($resultado) {
          header('Location: /');
        }
      }
    }

    $alertas = Usuario::getAlertas();
    $router->render('auth/recuperar-password', [
      'alertas' => $alertas,
      'error' => $error,
      'usuario' => $usuario
    ]);
  }



  /**
   * LOGOUT session
   * @return void
   */
  public function logout()
  {
    if (!isset($_SESSION)) {
      session_start();
    }

    $_SESSION = [];
    session_destroy();
    header('Location: /');
  }
}
