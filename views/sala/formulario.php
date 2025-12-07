<label>Dirección:</label>
<select name="id_direccion">
  <?php foreach ($direcciones as $d): ?>
    <option value="<?= $d->id ?>"
      <?= $sala->id_direccion == $d->id ? 'selected' : '' ?>>
      <?= $d->calle ?> <?= $d->num_calle ?> (<?= $d->localidad ?>)
    </option>
  <?php endforeach ?>
</select>

<label>Nº Sala:</label>
<input type="number" name="noSala" value="<?= $sala->noSala ?>">

<label>Nombre de la Sala:</label>
<input type="text" name="nombreSala" value="<?= $sala->nombreSala ?>">
