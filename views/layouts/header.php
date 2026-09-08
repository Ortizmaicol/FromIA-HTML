<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLogged = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'FormAI'; ?></title>

  <!-- Bootstrap 5 CSS & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800;900&display=swap" rel="stylesheet" />

  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" />

  <!-- Hoja de Estilos -->
  <link rel="stylesheet" href="/FromIA/Styles/Styles.css" />
</head>
<body>

<header class="Encabezado">
  <div class="Encabezado-navegacion">
    <h1 class="Encabezado-navegacion-Title">
      <a href="/FromIA/index.php" style="color: white; text-decoration: none;">Form-AI</a>
    </h1>

    <nav class="Encabezado-navegacion-Menu">
      <div class="Menu-Item">
        <a href="/FromIA/index.php">
          Inicio
          <svg viewBox="0 0 24 24"><path d="M7 17L17 7M17 7H7M17 7V17"/></svg>
        </a>
      </div>

      <div class="Menu-Item">
        <a href="/FromIA/500.php">
          Nosotros
          <svg viewBox="0 0 24 24"><path d="M7 17L17 7M17 7H7M17 7V17"/></svg>
        </a>
      </div>

      <div class="Menu-Item">
        <a href="/FromIA/membresias.php">
          Membresias
          <svg viewBox="0 0 24 24"><path d="M7 17L17 7M17 7H7M17 7V17"/></svg>
        </a>
      </div>

      <div class="Menu-Item">
        <a href="/FromIA/404.php">
          Contáctanos
          <svg viewBox="0 0 24 24"><path d="M7 17L17 7M17 7H7M17 7V17"/></svg>
        </a>
      </div>

      <?php if ($isLogged): ?>
        <div class="Menu-Item-Button">
          <a href="/FromIA/logout.php">Cerrar Sesión</a>
        </div>
      <?php else: ?>
        <div class="Menu-Item-Button">
          <a href="/FromIA/login.php">IniciarSesión/Registrarse</a>
        </div>
      <?php endif; ?>
    </nav>
  </div>
</header>