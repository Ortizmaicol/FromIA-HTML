<?php
$pageTitle = "FormAI - Dashboard";
$extraCss  = "dashboard.css";
require_once __DIR__ . '/../layouts/header.php';
?>

// Variables por defecto si no vienen del controlador
$nombreUsuario = isset($nombre) ? htmlspecialchars($nombre) : (isset($_SESSION['user_nombre']) ? htmlspecialchars($_SESSION['user_nombre']) : 'Laura');
$rolUsuario = isset($_SESSION['user_rol']) ? htmlspecialchars($_SESSION['user_rol']) : 'Cliente';
$proyectosList = isset($proyectos) && is_array($proyectos) ? $proyectos : [];
$totalProyectos = isset($totalActivos) ? (int)$totalActivos : count($proyectosList);
?>


<div class="dashboard-wrapper">
  <!-- SIDEBAR IZQUIERDO -->
  <aside class="sidebar-custom">
    <div>
      <div class="sidebar-group-title">PRINCIPAL</div>
      <ul class="sidebar-menu">
        <li>
          <a href="/FromIA/dashboard.php" class="active">
            <i class="bi bi-house-door-fill"></i> Inicio
          </a>
        </li>
        <li>
          <a href="#">
            <i class="bi bi-download"></i> Descargar App
            <span class="sidebar-badge-red">NUEVO</span>
          </a>
        </li>
        <li>
          <a href="#">
            <i class="bi bi-display"></i> Prueba Web Gratuita
          </a>
        </li>
      </ul>
    </div>

    <div>
      <div class="sidebar-group-title">MI CUENTA</div>
      <ul class="sidebar-menu">
        <li>
          <a href="/FromIA/membresias.php">
            <i class="bi bi-gem"></i> Mi Membresía
          </a>
        </li>
        <li>
          <a href="#">
            <i class="bi bi-folder-fill"></i> Mis Proyectos
            <span class="sidebar-badge-count"><?= $totalProyectos; ?></span>
          </a>
        </li>
        <li>
          <a href="#">
            <i class="bi bi-person-fill"></i> Mi Perfil
          </a>
        </li>
      </ul>
    </div>

    <div>
      <div class="sidebar-group-title">SOPORTE</div>
      <ul class="sidebar-menu">
        <li>
          <a href="#">
            <i class="bi bi-bug-fill"></i> Reportar Problema
          </a>
        </li>
        <li>
          <a href="#">
            <i class="bi bi-question-circle-fill"></i> Soporte
          </a>
        </li>
        <li>
          <a href="/FromIA/index.php" style="margin-top: 10px;">
            <i class="bi bi-globe"></i> Ir al sitio web
          </a>
        </li>
      </ul>
    </div>
  </aside>

  <!-- PANEL PRINCIPAL -->
  <main class="main-panel">
    <h1 class="welcome-title">
      Bienvenida, <?= $nombreUsuario; ?> 👋
    </h1>
    <p class="welcome-subtitle">
      Plan Profesional activo · Próxima factura: 14 de julio, 2026
    </p>

    <!-- 3 TARJETAS DE ESTADÍSTICAS -->
    <div class="stats-grid">
      <div class="stat-card-custom">
        <div class="stat-header">
          <span class="stat-header-label">PROYECTOS ACTIVOS</span>
          <i class="bi bi-folder stat-header-icon"></i>
        </div>
        <div class="stat-number"><?= $totalProyectos; ?></div>
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

    <!-- SECCIÓN PROYECTOS RECIENTES -->
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

      <?php if (empty($proyectosList)): ?>
        <div class="empty-projects-state">
          No tienes proyectos aún. ¡Crea el primero!
        </div>
      <?php else: ?>
        <?php foreach ($proyectosList as $p): ?>
          <div class="project-row">
            <span class="fw-semibold text-white">
              <i class="bi bi-file-earmark-text me-2 text-primary"></i>
              <?= htmlspecialchars($p['nomb_patron']); ?>
            </span>
            <span class="text-white-50"><?= htmlspecialchars($p['tipo_patron']); ?></span>
            <span class="text-white-50"><?= substr($p['fecha_creacion'], 0, 10); ?></span>
            <span style="text-align: right;">
              <span class="badge bg-secondary"><?= htmlspecialchars($p['estado_patron']); ?></span>
            </span>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </main>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>