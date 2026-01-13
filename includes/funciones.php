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
// function s($html): string
// {
//   $s = trim($html);
//   $s = stripslashes($html);

//   $s = htmlspecialchars($html);
//   return $s;
// }
function s($html): string
{
  $html = trim($html);
  $html = stripslashes($html);
  return htmlspecialchars($html, ENT_QUOTES, 'UTF-8');
}
