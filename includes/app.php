<?php

require 'funciones.php';
require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Model\ActiveRecord;


// dotENV -
try {
  $dotenv = Dotenv::createImmutable(dirname(__DIR__));
  // $dotenv->safeLoad();  // prod. - carga ignorando si existe ya que se asume otra inyección directa
  $dotenv->load();      // dev.
} catch (\Dotenv\Exception\InvalidPathException $e) {
  die("Error al cargar DotEnv: " . $e->getMessage());
}

require 'database.php';

// Conectarnos a la base de datos
ActiveRecord::setDB($db);
