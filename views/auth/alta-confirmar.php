<?php
include_once __DIR__ . "/../templates/alertas.php";

if (array_key_exists('error', $alertas)) {
  $sub_titulo = "ERROR de Confirmación";
  $mensaje_principal = "El token no es válido o ha expirado.";
  $mensaje_secundario = "Por favor, verifica el enlace o intenta registrarte de nuevo.";
  $clase_detalles = "alerta__detalles alerta__detalles--error";
} else if (array_key_exists('exito', $alertas)) {
  $sub_titulo = "Confirmada Cuenta";
  $mensaje_principal = "¡Tu cuenta ha sido confirmada con éxito!";
  $mensaje_secundario = "Ya puedes **iniciar sesión** con tus credenciales.";
  $clase_detalles = "alerta__detalles alerta__detalles--exito";
}
?>


<div class="auth col-1">

  <div class="auth__detalles">
    <h2>Bienvenido</h2>
    <h6 class="<?= $clase_detalles ?>"><?= $sub_titulo ?></h6>
    <p><?= $mensaje_principal ?></p>
    <br />
    <p><?= $mensaje_secundario ?></p>
  </div>

  <div class="acciones">
    <?php if (array_key_exists('exito', $alertas)): ?>
      <a href="/">Iniciar sesión</a>
    <?php else: ?>
      <a href="/olvide">¿Olvidaste tu contraseña?</a>
      <a href="/crear-cuenta">¿Todavía no tienes cuenta?</a>
    <?php endif; ?>
  </div>

</div>
