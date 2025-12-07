<div class="auth">

  <div class="auth__detalles">
    <h2>Bienvenido</h2>
    <h6>Authorizations for Access Controls</h6>
    <p>Recuperacion de credenciales personales del portal</p>
  </div>

  <div class="auth__campos">
    <h2>Recuperar Contraseña</h2>

    <?php
    // Mostrar el formulario SÓLO si NO hay error
    if (!$error) {
    ?>

      <form method="POST" id='form'>

        <div class="campo-input">
          <span>
            <i aria-hidden="true" class="fa fa-envelope"></i>
          </span>
          <input type="email"
            id="correo"
            name=""
            value="<?= $usuario->email ?>"
            disabled />
          <div class="error alerta-js"></div>
        </div>

        <div class="campo-input">
          <span>
            <i aria-hidden="true" class="fa fa-lock"></i>
          </span>
          <input type="password"
            id="contrasenna"
            placeholder="Contraseña"
            name="password"
            autocomplete="off" />
          <div class="error alerta-js"></div>
        </div>

        <div class="campo-input">
          <span>
            <i aria-hidden="true" class="fa fa-lock"></i>
          </span>
          <input type="password"
            id="contrasennaVerificar"
            placeholder="Confirmación de la contraseña"
            name="passwordCheck"
            autocomplete="off" />
          <div class="error alerta-js"></div>
        </div>

        <input type="submit" class='boton' value="Guadar Nueva Contraseña">
      </form>

    <?php
    } // Cierre del if (!$error)
    ?>

    <div class="acciones">
      <a href="/">Iniciar sesión</a>
      <a href="/crear-cuenta">¿Todavía no tienes cuenta?</a>
    </div>

  </div>
</div>


<?php
include_once __DIR__ . "/../templates/alertas.php";

$script = "
    <script defer src='assets/js/auth-recuperar.js'></script>
";
?>
