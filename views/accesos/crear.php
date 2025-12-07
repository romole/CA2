<h1><?php echo s($titulo); ?></h1>
<p>Rellena el formulario para enviar una nueva solicitud de acceso.</p>

<?php
// Muestra alertas si las hay
include_once __DIR__ . '/../templates/alertas.php';
?>

<form class="formulario" method="POST" action="/accesos/crear">

  <div class="campo">
    <label for="id_sala">Sala Requerida</label>
    <select id="id_sala" name="id_sala">
      <option value="">-- Seleccione --</option>
      <?php foreach ($salas as $sala): ?>
        <option
          value="<?php echo s($sala['id']); ?>"
          <?php echo $peticion->id_sala === $sala['id'] ? 'selected' : ''; ?>>
          <?php echo s($sala['nombreSala']); ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="campo">
    <label for="id_tipo_acceso">Tipo de Acceso</label>
    <select id="id_tipo_acceso" name="id_tipo_acceso">
      <option value="">-- Seleccione --</option>
      <?php foreach ($tipos_acceso as $tipo): ?>
        <option
          value="<?php echo s($tipo['id']); ?>"
          <?php echo $peticion->id_tipo_acceso === $tipo['id'] ? 'selected' : ''; ?>>
          <?php echo s($tipo['tipo']); ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="campo">
    <label for="fecha_vencimiento">Fecha de Vencimiento</label>
    <input
      type="date"
      id="fecha_vencimiento"
      name="fecha_vencimiento"
      value="<?php echo s($peticion->fecha_vencimiento); ?>" />
  </div>

  <div class="campo">
    <label for="justificacion">Justificación (Mín. 10 caracteres)</label>
    <textarea
      id="justificacion"
      name="justificacion"
      rows="5"><?php echo s($peticion->justificacion); ?></textarea>
  </div>

  <input type="submit" class="boton" value="Enviar Petición">
</form>

<style>
  /* Estilos básicos para el formulario */
  .formulario .campo {
    margin-bottom: 1.5rem;
  }

  .formulario label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: bold;
    color: var(--negro);
  }

  .formulario input[type="text"],
  .formulario input[type="email"],
  .formulario input[type="date"],
  .formulario select,
  .formulario textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ccc;
    border-radius: 0.375rem;
    box-sizing: border-box;
    /* Importante para el width: 100% */
  }

  .formulario textarea {
    resize: vertical;
  }

  .formulario .boton {
    background-color: var(--secundario);
    color: white;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 0.375rem;
    cursor: pointer;
    transition: background-color 0.3s;
    font-weight: bold;
  }

  .formulario .boton:hover {
    background-color: #059669;
  }

  /* emerald-600 */
</style>