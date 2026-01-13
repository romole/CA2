<?php
require_once __DIR__ . '/templates/head.php';
?>

<div class="container">
  <?= $contenido; ?>
</div>

<?php
echo $script ?? '';
require_once __DIR__ . '/templates/footer.php';
?>
