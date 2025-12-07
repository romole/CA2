<div class="auth">

  <div class="auth__detalles">
    <h2>Bienvenido</h2>
    <h6>Authorizations for Access Controls</h6>
    <p>Indique su credencial de personal para la recuperacion de contraseña</p>
    <p>Recibirás un e-mail que te permitirá editar tus datos</p>
  </div>

  <div class="auth__campos">
    <h2>Recuperar contraseña</h2>

    <form action="" method="POST" id='form'>

      <div class="campo-input">
        <span>
          <i aria-hidden="true" class="fa fa-envelope"></i>
        </span>
        <input type="email"
          id="correo"
          placeholder="Correo electrónico"
          name="email"
          autocomplete="off"
          required />
        <div class="error alerta-js"></div>
      </div>

      <input type="submit" class='boton' value="Recuperar contraseña">
    </form>

    <div class="acciones">
      <a href="/">Iniciar sesión</a>
      <a href="/crear-cuenta">¿Todavía no tienes cuenta?</a>
    </div>

  </div>

</div>


<?php
include_once __DIR__ . "/../templates/alertas.php";

$script = "
    <script defer src='assets/js/auth-olvido.js'></script>
  ";
?>
