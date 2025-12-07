<?php
include_once __DIR__ . '/../../templates/menu.php';

?>

<h1>Direcciones</h1>

<a class="btn" href="/direccion/crear">Nueva Dirección</a>

<table class="tabla">
  <thead>
    <tr>
      <th>id</th>
      <th>calle</th>
      <th>num.</th>
      <th>localidad</th>
      <th>cp</th>
      <th>provincia</th>
      <th>pais</th>
    </tr>
  </thead>

  <tbody>
    <?php foreach ($direcciones as $d): ?>
      <tr>
        <td><?= $d->id ?></td>
        <td><?= $d->calle ?></td>
        <td><?= $d->num_calle ?></td>
        <td><?= $d->localidad ?></td>
        <td><?= $d->cp ?></td>
        <td><?= $d->provincia ?></td>
        <td><?= $d->pais ?></td>

        <td>
          <a class="btn-small" href="/direccion/actualizar?id=<?= $d->id ?>">Editar</a>

          <form class="inline" method="POST" action="/direccion/eliminar">
            <input type="hidden" name="id" value="<?= $d->id ?>">
            <button class="btn-small danger">Eliminar</button>
          </form>
        </td>
      </tr>
    <?php endforeach ?>
  </tbody>
</table>
