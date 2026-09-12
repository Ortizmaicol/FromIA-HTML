<?php
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="dashboard-wrapper">
  <!-- Sidebar Cliente -->
  <aside class="sidebar-custom">
    <div>
      <div class="sidebar-group-title">PRINCIPAL</div>
      <ul class="sidebar-menu">
        <li><a href="/FromIA/dashboard.php" class="active"><i class="bi bi-house-door-fill"></i> Inicio</a></li>
        <li><a href="#"><i class="bi bi-download"></i> Descargar App <span class="sidebar-badge-red">NUEVO</span></a></li>
      </ul>
    </div>
    <div>
      <div class="sidebar-group-title">MI CUENTA</div>
      <ul class="sidebar-menu">
        <li><a href="/FromIA/membresias.php"><i class="bi bi-gem"></i> Mi Membresía</a></li>
        <li><a href="#"><i class="bi bi-folder-fill"></i> Mis Proyectos <span class="sidebar-badge-count"><?= $totalActivos; ?></span></a></li>
      </ul>
    </div>
  </aside>

  <!-- Contenido Cliente -->
  <main class="main-panel">
    <h1 class="welcome-title">Bienvenido(a), <?= htmlspecialchars($nombre); ?> 👋</h1>
    <p class="welcome-subtitle">Plan Activo · Espacio de trabajo personal</p>
    <!-- Métricas y Proyectos del cliente... -->
  </main>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>