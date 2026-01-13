<?php
// $isAuth = isAuth();
$nombreUsuario = $_SESSION['nombre'] ?? '';
$emailUsuario = $_SESSION['email'] ?? '';
$isAdmin = $_SESSION['admin'] ?? null;


require_once __DIR__ . '/templates/head.php';

?>

<div class="container">
  <?php echo $contenido; ?>
</div>

<?php
echo $script ?? '';

require_once __DIR__ . '/templates/footer.php';
?>
