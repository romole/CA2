<?php
include_once __DIR__ . '/../templates/menu.php';
?>

<div class="content-table">
  <table class="tabla">
    <caption><?php echo $titulo; ?></caption>

    <thead>
      <tr>
        <th>id</th>
        <th>estado</th>
        <th>tipo</th>

        <th>sala</th>
        <th>fecha inicio</th>
        <th>fecha fin</th>
        <th>motivo</th>
        <th>tecnico</th>

        <th>solicitante</th>
      </tr>
    </thead>
    <tbody>



      <!-- datos prueba -->
      <tr>
        <td data-label="id">1</td>
        <td data-label="estado"><span class="estado pendiente">Pendiente</span></td>
        <td data-label="tipo">Temporal</td>
        <td data-label="sala">Lorem ipsum dolor</td>
        <td data-label="fecha inicio">2025-12-05 10:00</td>
        <td data-label="fecha final">2025-12-06 10:00</td>
        <td data-label="motivo">Lorem ipsum dolor sit amet consectetur adipisicing elit</td>
        <td data-label="tecnico">loremYO</td>
        <td data-label="solicitante">Juan lorem</td>
      </tr>
      <tr>
        <td data-label="id">2</td>
        <td data-label="estado"><span class="estado aprobada">Aprobada</span></td>
        <td data-label="tipo">Regular</td>
        <td data-label="sala">Lorem ipsum dolor</td>
        <td data-label="fecha inicio">2025-12-04 10:00</td>
        <td data-label="fecha final">2026-06-04 10:00</td>
        <td data-label="motivo">Lorem ipsum dolor sit amet consectetur adipisicing elit</td>
        <td data-label="tecnico">loremYO</td>
        <td data-label="solicitante">Ana lorem</td>
      </tr>
      <tr>
        <td data-label="id">3</td>
        <td data-label="estado"><span class="estado denegada">Denegada</span></td>
        <td data-label="tipo">Visita</td>
        <td data-label="sala">Lorem ipsum dolor</td>
        <td data-label="fecha inicio">2025-12-06 10:00</td>
        <td data-label="fecha final">2025-12-06 13:00</td>
        <td data-label="motivo">Lorem ipsum dolor sit amet consectetur adipisicing elit</td>
        <td data-label="tecnico">loremYO</td>
        <td data-label="solicitante">Pedro lorem</td>
      </tr>
      <!-- datos prueba -->

      

      <?php if (empty($peticiones)): ?>
        <tr>
          <td colspan="9" class="td-empty alerta__detalles alerta__detalles--error">
            No hay peticiones de acceso registradas
          </td>
        </tr>

        <?php else:
        foreach ($peticiones as $peticion): ?>
          <tr>
            <td data-label="id"><?= $peticion->id; ?></td>
            <td data-label="estado"><?= $peticion->estadoAcceso; ?></td>
            <td data-label="tipo"><?= $peticion->tipoAcceso; ?></td>
            <td data-label="sala"><?= $peticion->nombreSala; ?></td>
            <td data-label="	fecha inicio"><?= date('d/m/Y', strtotime($peticion->fecha_solicitud)); ?></td>
            <td data-label="fecha final"><?= date('d/m/Y', strtotime($peticion->fecha_vencimiento)); ?></td>
            <td data-label="motivo"><?= substr($peticion->motivo, 0, 50) . '...'; ?></td>
            <td data-label="tecnico"><?= substr($peticion->tecnico, 0, 50) . '...'; ?></td>

            <td data-label="solicitante"><?= $peticion->nombrePeticionario; ?></td>
          </tr>
      <?php endforeach;

      endif; ?>
    </tbody>
  </table>
</div>

<?php
include_once __DIR__ . '/../templates/alertas.php';
?>
