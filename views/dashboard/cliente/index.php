<?php
$pageTitle    = "FormAI - Mi Espacio";
$extraCss     = "dashboard.css";
$nombre       = $nombre ?? ($_SESSION['user_nombre'] ?? 'Cliente');
$totalActivos = $totalActivos ?? (isset($proyectos) && is_array($proyectos) ? count($proyectos) : 0);

require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="dashboard-wrapper">
  <!-- Sidebar Cliente -->
  <aside class="sidebar-custom">
    <div>
      <div class="sidebar-group-title">PRINCIPAL</div>
      <ul class="sidebar-menu">
        <li><a href="/dashboard.php" class="active"><i class="bi bi-house-door-fill"></i> Inicio</a></li>
        <li><a href="#"><i class="bi bi-download"></i> Descargar App <span class="sidebar-badge-red">NUEVO</span></a></li>
        <li><a href="#"><i class="bi bi-display"></i> Prueba Web Gratuita</a></li>
      </ul>
    </div>
    <div>
      <div class="sidebar-group-title">MI CUENTA</div>
      <ul class="sidebar-menu">
        <li><a href="/membresias.php"><i class="bi bi-gem"></i> Mi Membresía</a></li>
        <li><a href="#"><i class="bi bi-folder-fill"></i> Mis Proyectos <span class="sidebar-badge-count"><?= (int)$totalActivos; ?></span></a></li>
        <li><a href="#"><i class="bi bi-person-fill"></i> Mi Perfil</a></li>
      </ul>
    </div>
    <div>
      <div class="sidebar-group-title">SOPORTE</div>
      <ul class="sidebar-menu">
        <li><a href="#"><i class="bi bi-bug-fill"></i> Reportar Problema</a></li>
        <li><a href="#"><i class="bi bi-question-circle-fill"></i> Soporte</a></li>
        <li><a href="/index.php" style="margin-top: 10px;"><i class="bi bi-globe"></i> Ir al sitio web</a></li>
      </ul>
    </div>
  </aside>

  <!-- Contenido Cliente -->
  <main class="main-panel">
    <h1 class="welcome-title">Bienvenida, <?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8'); ?> 👋</h1>
    <p class="welcome-subtitle">Plan Profesional activo · Próxima factura: 14 de julio, 2026</p>

    <!-- Métricas del Cliente -->
    <div class="stats-grid">
      <div class="stat-card-custom">
        <div class="stat-header">
          <span class="stat-header-label">PROYECTOS ACTIVOS</span>
          <i class="bi bi-folder stat-header-icon"></i>
        </div>
        <div class="stat-number"><?= (int)$totalActivos; ?></div>
        <span class="stat-pill stat-pill-green">+2 este mes</span>
      </div>

      <div class="stat-card-custom">
        <div class="stat-header">
          <span class="stat-header-label">EXPORTACIONES</span>
          <i class="bi bi-box-arrow-up-right stat-header-icon"></i>
        </div>
        <div class="stat-number">14</div>
        <span class="stat-pill stat-pill-green">este mes</span>
      </div>

      <div class="stat-card-custom">
        <div class="stat-header">
          <span class="stat-header-label">DÍAS RESTANTES</span>
          <i class="bi bi-clock stat-header-icon"></i>
        </div>
        <div class="stat-number">22</div>
        <span class="stat-pill stat-pill-gray">del periodo</span>
      </div>
    </div>

    <!-- Proyectos recientes -->
    <div class="projects-box">
      <div class="projects-top">
        <h2>Proyectos recientes</h2>
        <a href="#">Ver todos &rarr;</a>
      </div>

      <div class="projects-table-header">
        <span>PROYECTO</span>
        <span>TIPO</span>
        <span>MODIFICADO</span>
        <span style="text-align: right;">ACCIONES</span>
      </div>

      <div class="empty-projects-state">
        No tienes proyectos aún. ¡Crea el primero!
      </div>
    </div>
  </main>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>