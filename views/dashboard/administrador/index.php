<?php
$pageTitle = "FormAI - Panel de Administración";
$extraCss  = "dashboard.css";
$nombre    = $nombre ?? ($_SESSION['user_nombre'] ?? 'Administrador');

require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="dashboard-wrapper">
  <!-- Sidebar Administrador -->
  <aside class="sidebar-custom">
    <div>
      <div class="sidebar-group-title">GESTIÓN GENERAL</div>
      <ul class="sidebar-menu">
        <li><a href="/dashboard.php" class="active"><i class="bi bi-speedometer2"></i> Métricas Globales</a></li>
        <li><a href="#"><i class="bi bi-people-fill"></i> Usuarios del Sistema</a></li>
        <li><a href="#"><i class="bi bi-credit-card-fill"></i> Historial de Pagos</a></li>
        <li><a href="#"><i class="bi bi-sliders"></i> Configuración</a></li>
      </ul>
    </div>
  </aside>

  <!-- Contenido Administrador -->
  <main class="main-panel">
    <h1 class="welcome-title">Panel de Control General ⚙️</h1>
    <p class="welcome-subtitle">Sesión administrativa iniciada por <?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8'); ?></p>

    <div class="stats-grid">
      <div class="stat-card-custom">
        <div class="stat-header">
          <span class="stat-header-label">USUARIOS REGISTRADOS</span>
          <i class="bi bi-people stat-header-icon"></i>
        </div>
        <div class="stat-number">8</div>
        <span class="stat-pill stat-pill-green">Base de datos</span>
      </div>

      <div class="stat-card-custom">
        <div class="stat-header">
          <span class="stat-header-label">INGRESOS DEL MES</span>
          <i class="bi bi-cash-stack stat-header-icon"></i>
        </div>
        <div class="stat-number">$1,240</div>
        <span class="stat-pill stat-pill-green">COP</span>
      </div>

      <div class="stat-card-custom">
        <div class="stat-header">
          <span class="stat-header-label">PATRONES PROCESADOS</span>
          <i class="bi bi-cpu stat-header-icon"></i>
        </div>
        <div class="stat-number">45</div>
        <span class="stat-pill stat-pill-gray">Total histórico</span>
      </div>
    </div>
  </main>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>