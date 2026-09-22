<?php
$pageTitle = "404 - Página no encontrada";
$extraCss  = "error.css";
require_once __DIR__ . '/../layouts/header.php';
?>


<main class="error-wrapper">
  <div class="error-num">404</div>
  <h2 class="error-heading">Página no encontrada</h2>
  <p class="error-paragraph">
    Lo sentimos, la sección de contáctanos no está disponible en este momento o fue reubicada.
  </p>
  <a href="/FromIA-HTML/index.php" class="btn-volver-inicio">
    &larr; Volver al Inicio
  </a>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>