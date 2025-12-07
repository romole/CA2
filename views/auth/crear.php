<div class="auth">

  <div class="auth__detalles">
    <h2>Bienvenido</h2>
    <h6>Authorizations for Access Controls</h6>
    <p>Unete y solicite tus autorizaciones para las areas con control de acceso</p>
  </div>

  <div class="auth__campos">
    <h2>Crear cuenta</h2>
    <form action="/crear-cuenta" method="POST" id='form'>

      <div class="campo-input">
        <span>
          <i aria-hidden="true" class="fa fa-user"></i>
        </span>
        <input type="text"
          id="nombre"
          placeholder="Nombre de usuario"
          name="nombre"
          value="<?php echo s($usuario->nombre); ?>" />
        <div class="error alerta-js"> </div>
      </div>

      <div class="campo-input">
        <span>
          <i aria-hidden="true" class="fa fa-envelope"></i>
        </span>
        <input type="email"
          id="correo"
          placeholder="Correo electrónico"
          name="email"
          value="<?php echo s($usuario->email); ?>" />
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

      <fieldset>
        <label class="campo-checkbox" for="ckeckBox1">
          <input type="checkbox"
            id="ckeckBox1"
            name="c-terminos"
            value="1" />
          <span>Términos y condiciones del portal</span>
        </label>

        <label class="campo-checkbox" for="ckeckBox2">
          <input type="checkbox"
            id="ckeckBox2"
            name="c-privacidad"
            value="1" />
          <span>He leído y acepto la política de privacidad</span>
        </label>
      </fieldset>

      <input type="submit" class='boton' value="Crear cuenta">
    </form>

    <div class="acciones">
      <a href="/olvide">¿Olvidaste tu contraseña?</a>
      <a href="/">Iniciar sesión</a>
    </div>

  </div>
  
</div>


<?php
include_once __DIR__ . "/../templates/alertas.php";

$script = "
    <script defer src='assets/js/auth-crear.js'></script>
  ";
?>
