<?php
$pageTitle = "404 - Página no encontrada";
require_once __DIR__ . '/../layouts/header.php';
?>

<style>
  body {
    background-color: #000000;
    color: #ffffff;
    margin: 0;
    font-family: 'Montserrat', sans-serif;
  }

  .error-wrapper {
    min-height: calc(100vh - 70px);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 20px;
    box-sizing: border-box;
  }

  .error-num {
    font-family: 'Montserrat', sans-serif;
    font-size: clamp(6.5rem, 15vw, 10.5rem);
    font-weight: 900;
    line-height: 1;
    margin: 0 0 10px 0;
    letter-spacing: -2px;
    background: linear-gradient(135deg, #3d6cb9 0%, #5d4db8 50%, #8755c2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }

  .error-heading {
    font-family: 'Montserrat', sans-serif;
    font-size: clamp(1.8rem, 3.5vw, 2.5rem);
    font-weight: 700;
    color: #ffffff;
    margin: 0 0 14px 0;
    letter-spacing: -0.5px;
  }

  .error-paragraph {
    color: #a0a0a0;
    font-size: clamp(0.95rem, 1.8vw, 1.05rem);
    font-weight: 400;
    max-width: 520px;
    line-height: 1.5;
    margin: 0 auto 32px;
  }

  .btn-volver-inicio {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background-color: #336699;
    color: #ffffff;
    font-family: 'Montserrat', sans-serif;
    font-size: 0.95rem;
    font-weight: 600;
    padding: 12px 28px;
    border-radius: 24px;
    text-decoration: none;
    transition: background-color 0.25s ease, transform 0.2s ease;
  }

  .btn-volver-inicio:hover {
    background-color: #264d73;
    color: #ffffff;
    transform: translateY(-1px);
  }
</style>

<main class="error-wrapper">
  <div class="error-num">404</div>
  <h2 class="error-heading">Página no encontrada</h2>
  <p class="error-paragraph">
    Lo sentimos, la sección de contáctanos no está disponible en este momento o fue reubicada.
  </p>
  <a href="/FromIA/index.php" class="btn-volver-inicio">
    &larr; Volver al Inicio
  </a>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>