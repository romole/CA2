<h1>Salas</h1>

<a class="btn" href="/sala/crear">Nueva Sala</a>

<table class="tabla">
  <thead>
    <tr>
      <th>ID</th>
      <th>Dirección</th>
      <th>Nº Sala</th>
      <th>Nombre</th>
      <th>Acciones</th>
    </tr>
  </thead>

  <tbody>
    <?php foreach ($salas as $s): ?>
      <tr>
        <td><?= $s->id ?></td>
        <td><?= $s->direccion()->calle ?> <?= $s->direccion()->num_calle ?></td>
        <td><?= $s->noSala ?></td>
        <td><?= $s->nombreSala ?></td>

        <td>
          <a class="btn-small" href="/sala/actualizar?id=<?= $s->id ?>">Editar</a>

          <form method="POST" action="/sala/eliminar" class="inline">
            <input type="hidden" name="id" value="<?= $s->id ?>">
            <button class="btn-small danger">Eliminar</button>
          </form>
        </td>
      </tr>
    <?php endforeach ?>
  </tbody>
</table>
