<?php
if (!function_exists('isAuth')) {
  require_once __DIR__ . '/../../includes/funciones.php';
}

if (isAuth()):
?>

  <div class="sidebar">
    <div class="logo">
      <a href="/accesos">Logo</a>
    </div>

    <ul class="menu">
      <!-- menu administrador -->
      <li>
        <?php if (isAdmin()): ?>
          <ul class="menu__admin">
            <li class="menu-li">
              <a href="/direccion">Direcciones</a>
            </li>

            <li class="menu-li">
              <a href="/sala">Salas</a>
            </li>
          </ul>
        <?php endif; ?>
      </li>

      <!-- menu -->
      <li>
        <a href="/accesos/crear-peticion" class="header__link">
          <i class="fa fa-plus fa-sm"></i>Petición
        </a>
      </li>
      <li class="menu-session">
        <span>
          <?= $_SESSION['nombre'] ?? '' ?>
        </span>
        <a class="link-bold" href="/logout">
          <i class="fa-sharp fa-solid fa-right-from-bracket fa-xl"></i>
        </a>
      </li>

    </ul>
  </div>

<?php endif; ?>
