<?php
require_once __DIR__ . '/../includes/app.php';

// iniciar sesion globalmente
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

use MVC\Router;
use Controllers\LoginController;
use Controllers\AccesosController;
use Controllers\AdminController;


$router = new Router();


// session login / logout
$router->get('/', [LoginController::class, 'login']);
$router->post('/', [LoginController::class, 'login']);
$router->get('/logout', [LoginController::class, 'logout']);

// olvide cuenta.. envio password
$router->get('/olvide', [LoginController::class, 'olvide']);
$router->post('/olvide', [LoginController::class, 'olvide']);
// repuperar cuenta.. validacion password
$router->get('/recuperar', [LoginController::class, 'recuperar']);
$router->post('/recuperar', [LoginController::class, 'recuperar']);

// crear cuenta
$router->get('/crear-cuenta', [LoginController::class, 'crear']);
$router->post('/crear-cuenta', [LoginController::class, 'crear']);
$router->get('/alta-mensaje', [LoginController::class, 'altaMensaje']);

// crear cuenta.. confirmar cuenta
$router->get('/alta-confirmar', [LoginController::class, 'altaConfirmar']);



// desarrollando ..
//
// ----------------------------------------------------------------
// zona PRIVADA
// ----------------------------------------------------------------

// rutas General (AccesosController)
$router->get('/accesos', [AccesosController::class, 'index']);
$router->post('/accesos', [AccesosController::class, 'index']);

$router->get('/accesos/crear-peticion', [AccesosController::class, 'crearPeticion']);
$router->post('/accesos/crear-peticion', [AccesosController::class, 'crearPeticion']);


//
// rutas Administrador (AccesosController)
$router->get('/admin', [AdminController::class, 'index']);
// CRUD direcciones
$router->get('/admin/direcciones', [AdminController::class, 'direcciones']);
$router->get('/admin/direcciones/crear', [AdminController::class, 'crearDireccion']);
$router->post('/admin/direcciones/crear', [AdminController::class, 'crearDireccion']);
$router->get('/admin/direcciones/actualizar', [AdminController::class, 'actualizarDireccion']);
$router->post('/admin/direcciones/actualizar', [AdminController::class, 'actualizarDireccion']);
// CRUD salas
$router->get('/admin/salas', [AdminController::class, 'salas']);
$router->get('/admin/salas/crear', [AdminController::class, 'crearSala']);
$router->post('/admin/salas/crear', [AdminController::class, 'crearSala']);
$router->get('/admin/salas/actualizar', [AdminController::class, 'actualizarSala']);
$router->post('/admin/salas/actualizar', [AdminController::class, 'actualizarSala']);



$router->comprobarRutas();
