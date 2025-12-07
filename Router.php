<?php

namespace MVC;

class Router
{
  public array $getRoutes = [];
  public array $postRoutes = [];

  public function get($url, $fn)
  {
    $this->getRoutes[$url] = $fn;
  }

  public function post($url, $fn)
  {
    $this->postRoutes[$url] = $fn;
  }


  public function comprobarRutas()
  {
    // limpiar parametro GET
    $url_actual = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
      $fn = $this->getRoutes[$url_actual] ?? null;
    } else {
      $fn = $this->postRoutes[$url_actual] ?? null;
    }

    if ($fn) {
      // instanciar clase del controlador + ejecuta construct() BaseController (si hereda)
      $controllerInstance = new $fn[0];
      // nombre del metodo del array
      $methodName = $fn[1];

      // llamar al metodo instanciado
      call_user_func([$controllerInstance, $methodName], $this);

    } else {
      // redirigir al gogin si 404
      // echo "Página No Encontrada o Ruta no válida";
      header('Location: /');
    }
  }

  public function render($view, $datos = [])
  {
    foreach ($datos as $key => $value) {
      $$key = $value;
    }

    ob_start();

    include_once __DIR__ . "/views/$view.php";
    $contenido = ob_get_clean();
    include_once __DIR__ . '/views/layout.php';
  }
}
