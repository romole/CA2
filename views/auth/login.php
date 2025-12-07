<div class="auth">

  <div class="auth__detalles">
    <h2>Bienvenido</h2>
    <h6>Authorizations for Access Controls</h6>
    <p>Indique su credenciales personales para acceder al portal</p>
  </div>

  <div class="auth__campos">
    <h2>Iniciar sesión</h2>

    <form action="/" method="POST" id='form'>

      <div class="campo-input">
        <span>
          <i aria-hidden="true" class="fa fa-envelope"></i>
        </span>
        <input type="email"
          id="correo"
          placeholder="Correo electrónico"
          name="email"
          required />
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
          autocomplete="on"
          required />
        <div class="error alerta-js"></div>
      </div>

      <input type="submit" class='boton' value="Iniciar sesión">
    </form>

    <div class="acciones">
      <a href="/olvide">¿Olvidaste tu contraseña?</a>
      <a href="/crear-cuenta">¿Todavía no tienes cuenta?</a>
    </div>

  </div>
  
</div>

<?php
include_once __DIR__ . "/../templates/alertas.php";

$script = "
    <script defer src='assets/js/auth-login.js'></script>
  ";
?>
