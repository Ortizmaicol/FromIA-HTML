<?php
$pageTitle = "FormAI - 500 Internal Server Error";
require_once __DIR__ . '/../layouts/header.php';
?>

<main class="d-flex flex-column align-items-center justify-content-center text-center p-4" style="min-height: 80vh;">
  <h1 class="error-code" style="color: #ff3b30; text-shadow: 0 0 25px rgba(255, 59, 48, 0.3); font-size: clamp(5rem, 12vw, 9rem); font-weight: 900;">500</h1>
  <h2 class="error-title">Error Interno del Servidor</h2>
  <p class="error-desc">
    Lo sentimos, ocurriÃ³ un problema inesperado en los servidores de FormAI al procesar la secciÃ³n "Nosotros". Estamos trabajando para solucionarlo.
  </p>
  <a href="index.php" class="btn-back">Volver al Inicio</a>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
