<?php

namespace MVC;

/**
 * manejador de rutas
 *   registrar rutas GET y POST + verificar la actual
 *   ejecutar controladores o closures asociados, y renderizar vistas
 */
class Router
{
  public array $getRoutes = [];
  public array $postRoutes = [];

  /**
   * rutas GET
   * @param string $url URL de la ruta (.. "/home")
   * @param callable|array $fn Closure o array [controlador, metodo]
   */
  public function get($url, $fn)
  {
    $this->getRoutes[$url] = $fn;
  }
  /**
   * rutas POST
   * @param string $url URL de la ruta
   * @param callable|array $fn Closure o array [controlador, metodo]
   */
  public function post($url, $fn)
  {
    $this->postRoutes[$url] = $fn;
  }

  // compropar ruta y ejecuta el handler correspondiente
  public function comprobarRutas()
  {
    // obtener URL y limpiar parametro GET
    $url_actual = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
    $method = $_SERVER['REQUEST_METHOD'];

    // selecciona el handler segun el metodo HTTP (GET / POST)
    if ($method === 'GET') {
      $fn = $this->getRoutes[$url_actual] ?? null;
    } else {
      $fn = $this->postRoutes[$url_actual] ?? null;
    }

    // error 404 -> ruta no registrada
    if ($fn === null) {
      http_response_code(404);

      $env = $_ENV['APP_ENV'] ?? 'prod';

      if ($env === 'dev') {
        // modo dev. > muestra rutas registradas y URL buscada
        $this->debugRutaNoEncontrada($url_actual, $method);
      } else {
        // modo pro. > muestra pagina 404
        include_once __DIR__ . '/views/errors/404.php';
      }

      exit;
    }

    // si es un Closure (__routes) > ejecuta directamente
    if ($fn instanceof \Closure) {
      call_user_func($fn);
      return;
    }

    // controlador clasico [clase, metodo]
    if (is_array($fn) && class_exists($fn[0])) {
      // instanciar clase del controlador + ejecuta construct() BaseController (si hereda)
      $controllerInstance = new $fn[0];
      // nombre del metodo a ejecutar del array
      $methodName = $fn[1];

      // llamada al instanciado
      call_user_func([$controllerInstance, $methodName], $this);
      return;
    }

    // DEV -- error de rutas
    if (($_ENV['APP_ENV'] ?? 'prod') === 'dev') {
      throw new \Exception('Handler de ruta no válida, controlador o método asociado no es válido');
    }

    // PROD -- error interno
    http_response_code(500);
    include_once __DIR__ . '/views/errors/500.php';
    exit;
  }

  /**
   * muestra info.de.debug cuando una ruta no esta registrada
   * @param string $url URL buscada
   * @param string $method metodo HTTP
   */
  private function debugRutaNoEncontrada(string $url, string $method): void
  {
    echo "<h1>🚫 Ruta no registrada</h1>";
    echo "<p><strong>Método:</strong> {$method}</p>";
    echo "<p><strong>URL:</strong> {$url}</p>";

    echo "<h3>Rutas GET disponibles:</h3><ul>";
    foreach (array_keys($this->getRoutes) as $route) {
      echo "<li>GET {$route}</li>";
    }
    echo "</ul>";

    echo "<h3>Rutas POST disponibles:</h3><ul>";
    foreach (array_keys($this->postRoutes) as $route) {
      echo "<li>POST {$route}</li>";
    }
    echo "</ul>";
  }

  // debugging - listar todas las rutas registradas en el sistema
  public function listarRutas(): void
  {
    echo "<h1>Rutas registradas</h1>";

    echo "<h2>GET</h2><ul>";
    foreach ($this->getRoutes as $url => $handler) {
      if ($handler instanceof \Closure) {
        echo "<li>{$url} → Closure</li>";
      } elseif (is_array($handler)) {
        echo "<li>{$url} → {$handler[0]}::{$handler[1]}</li>";
      }
    }
    echo "</ul>";

    echo "<h2>POST</h2><ul>";
    foreach ($this->postRoutes as $url => $handler) {
      if ($handler instanceof \Closure) {
        echo "<li>{$url} → Closure</li>";
      } elseif (is_array($handler)) {
        echo "<li>{$url} → {$handler[0]}::{$handler[1]}</li>";
      }
    }
    echo "</ul>";
  }

  /**
   * renderizar vista en el layout
   * @param string $view Nombre de la vista
   * @param array $datos Variables que se pasan a la vista
   */
  public function render(string $view, array $datos = []): void
  {
    // variables del array $datos como variables individuales
    foreach ($datos as $key => $value) {
      $$key = $value;
    }

    // captura la salida de la vista
    ob_start();
    include_once __DIR__ . "/views/$view.php";
    $contenido = ob_get_clean();

    // incluye layout.. $contenido
    include_once __DIR__ . '/views/layout.php';
  }
}
