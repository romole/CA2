<?php

declare(strict_types=1);
// --mostrar errores
ini_set('log_errors', '1');
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
// ini_set('error_reporting', E_ALL);
// error_reporting(E_ALL);
// --rest of the code


function debuguear($variable, $out = true)
{
  echo '<pre>';
  var_dump($variable);
  echo "</pre>";

  if ($out) exit();
}


// Escapa / Sanitizar el HTML
function s($html): string
{
  $s = trim($html);
  $s = stripslashes($html);

  $s = htmlspecialchars($html);
  return $s;
}

// verifica autenticado de usuario
function isAuth()
{
  if (!isset($_SESSION)) {
    session_start();
  }

  if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: /');
    exit();
  }
  return true;
}

// verifica autenticado de admisnistrador
function isAdmin()
{
  return isset($_SESSION['admin']) && $_SESSION['admin'] === '1';
}

// verifica autenticado y redirigir si NO es administrador
function isAdminOrRedirect(string $path = '/')
{
  if (!isAdmin()) {
    header("Location: $path");
    exit();
  }
}
