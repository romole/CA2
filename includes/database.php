<?php

$host = $_ENV['DB_HOST'] ?? 'localhost';
$user = $_ENV['DB_USER'] ?? '';
$pass = $_ENV['DB_PASS'] ?? '';
$name = $_ENV['DB_NAME'] ?? '';

$db = new mysqli($host, $user, $pass, $name);

if (!$db) {
    echo "<b>error de conexion a la base de datos</b><br />";
    die("codigo ERRNO >> " . mysqli_connect_errno() .
        "<br />descripcion ERROR >> " . mysqli_connect_error());
}
mysqli_set_charset($db, "utf8");
