<?php
$pageTitle = "FormAI - Dashboard";
require_once __DIR__ . '/../layouts/header.php';

// Variables por defecto si no vienen del controlador
$nombreUsuario = isset($nombre) ? htmlspecialchars($nombre) : (isset($_SESSION['user_nombre']) ? htmlspecialchars($_SESSION['user_nombre']) : 'Laura');
$rolUsuario = isset($_SESSION['user_rol']) ? htmlspecialchars($_SESSION['user_rol']) : 'Cliente';
$proyectosList = isset($proyectos) && is_array($proyectos) ? $proyectos : [];
$totalProyectos = isset($totalActivos) ? (int)$totalActivos : count($proyectosList);
?>

<style>
  body {
    background-color: #05020c;
    color: #ffffff;
    font-family: 'Montserrat', sans-serif;
    margin: 0;
    overflow-x: hidden;
  }

  .dashboard-wrapper {
    display: flex;
    min-height: calc(100vh - 65px);
  }

  /* SIDEBAR LATERAL */
  .sidebar-custom {
    width: 260px;
    min-width: 260px;
    background-color: #080314;
    border-right: 1px solid rgba(255, 255, 255, 0.06);
    padding: 24px 16px;
    display: flex;
    flex-direction: column;
    gap: 22px;
  }

  .sidebar-group-title {
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.8px;
    color: #5d5a75;
    margin-bottom: 10px;
    padding-left: 12px;
  }

  .sidebar-menu {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 4px;
  }

  .sidebar-menu li a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 14px;
    color: #9b98b0;
    text-decoration: none;
    font-size: 0.88rem;
    font-weight: 500;
    border-radius: 8px;
    transition: all 0.2s ease;
  }

  .sidebar-menu li a:hover {
    color: #ffffff;
    background-color: rgba(255, 255, 255, 0.04);
  }

  .sidebar-menu li a.active {
    background-color: #2c1e50;
    color: #ffffff;
    font-weight: 600;
  }

  .sidebar-badge-red {
    background-color: #e62e2e;
    color: #ffffff;
    font-size: 0.65rem;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 10px;
    margin-left: auto;
  }

  .sidebar-badge-count {
    background-color: #3b2866;
    color: #ffffff;
    font-size: 0.75rem;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 12px;
    margin-left: auto;
  }

  /* CONTENIDO PRINCIPAL */
  .main-panel {
    flex: 1;
    padding: 36px 45px;
    background: radial-gradient(circle at top right, rgba(35, 20, 65, 0.3) 0%, #05020c 70%);
  }

  .welcome-title {
    font-size: 2.2rem;
    font-weight: 800;
    color: #ffffff;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .welcome-subtitle {
    color: #8c89a4;
    font-size: 0.95rem;
    margin-bottom: 32px;
  }

  /* TARJETAS DE ESTADÍSTICAS */
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 20px;
    margin-bottom: 35px;
  }

  .stat-card-custom {
    background-color: #0f0a21;
    border: 1px solid rgba(255, 255, 255, 0.06);
    border-radius: 14px;
    padding: 22px 24px;
    position: relative;
  }

  .stat-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
  }

  .stat-header-label {
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.6px;
    color: #8a87a2;
  }

  .stat-header-icon {
    font-size: 1.15rem;
    color: #686482;
  }

  .stat-number {
    font-size: 2.5rem;
    font-weight: 800;
    color: #ffffff;
    line-height: 1;
    margin-bottom: 14px;
  }

  .stat-pill {
    display: inline-block;
    font-size: 0.72rem;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 12px;
  }

  .stat-pill-green {
    background-color: #0e2e21;
    color: #2ecc71;
  }

  .stat-pill-gray {
    background-color: #1b172d;
    color: #8c89a4;
  }

  /* SECCIÓN PROYECTOS */
  .projects-box {
    background-color: #0f0a21;
    border: 1px solid rgba(255, 255, 255, 0.06);
    border-radius: 14px;
    overflow: hidden;
  }

  .projects-top {
    padding: 20px 26px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .projects-top h2 {
    font-size: 1.25rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0;
  }

  .projects-top a {
    color: #8c89a4;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 600;
    transition: color 0.2s ease;
  }

  .projects-top a:hover {
    color: #ffffff;
  }

  .projects-table-header {
    background-color: #0b071a;
    padding: 12px 26px;
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.6px;
    color: #6d6a87;
    border-top: 1px solid rgba(255, 255, 255, 0.04);
  }

  /* CONTENEDOR VACÍO BLANCO */
  .empty-projects-state {
    background-color: #ffffff;
    color: #777777;
    text-align: center;
    padding: 45px 20px;
    font-size: 0.95rem;
    font-weight: 500;
  }

  /* FILA CON PROYECTOS REALES (SI EXISTEN EN MySQL) */
  .project-row {
    padding: 14px 26px;
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr;
    align-items: center;
    border-bottom: 1px solid rgba(255, 255, 255, 0.04);
    font-size: 0.88rem;
  }

  @media (max-width: 900px) {
    .dashboard-wrapper {
      flex-direction: column;
    }
    .sidebar-custom {
      width: 100%;
      min-width: 100%;
      border-right: none;
      border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }
    .main-panel {
      padding: 24px 16px;
    }
  }
</style>

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