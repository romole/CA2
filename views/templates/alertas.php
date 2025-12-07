<div class="alertas-contenedor">
  <?php
  if (isset($alertas) && is_array($alertas)) {
    foreach ($alertas as $tipoAlerta => $mensajes) {
      foreach ($mensajes as $mensaje) {
  ?>
        <div class="alerta <?php echo $tipoAlerta; ?>">
          <?php echo s($mensaje); ?>
        </div>
  <?php
      }
    }
  }
  ?>
</div>
