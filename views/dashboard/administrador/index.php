<?php
$pageTitle         = "FormAI - Panel de Administración";
$extraCss          = "dashboard.css";
$nombre            = $nombre ?? ($_SESSION['user_nombre'] ?? 'Administrador');
$totalUsuarios     = $totalUsuarios ?? 0;
$totalIngresos     = $totalIngresos ?? 0.0;
$totalPatrones     = $totalPatrones ?? 0;
$usuariosRecientes = $usuariosRecientes ?? [];

require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="dashboard-wrapper">
  <!-- Sidebar Administrador -->
  <aside class="sidebar-custom">
    <div>
      <div class="sidebar-group-title">GESTIÓN GENERAL</div>
      <ul class="sidebar-menu">
        <li><a href="/FromIA-HTML/dashboard.php" class="active"><i class="bi bi-speedometer2"></i> Métricas Globales</a></li>
        <li><a href="/FromIA-HTML/dashboard.php?seccion=usuarios"><i class="bi bi-people-fill"></i> Usuarios del Sistema</a></li>
        <li><a href="/FromIA-HTML/dashboard.php?seccion=pagos"><i class="bi bi-credit-card-fill"></i> Historial de Pagos</a></li>
        <li><a href="/FromIA-HTML/dashboard.php?seccion=reportes"><i class="bi bi-file-earmark-bar-graph"></i> Reportes</a></li>
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
        <div class="stat-number"><?= (int)$totalUsuarios; ?></div>
        <span class="stat-pill stat-pill-green">Base de datos</span>
      </div>

      <div class="stat-card-custom">
        <div class="stat-header">
          <span class="stat-header-label">INGRESOS TOTALES</span>
          <i class="bi bi-cash-stack stat-header-icon"></i>
        </div>
        <div class="stat-number">$<?= number_format((float)$totalIngresos, 2); ?></div>
        <span class="stat-pill stat-pill-green">USD</span>
      </div>

      <div class="stat-card-custom">
        <div class="stat-header">
          <span class="stat-header-label">PATRONES PROCESADOS</span>
          <i class="bi bi-cpu stat-header-icon"></i>
        </div>
        <div class="stat-number"><?= (int)$totalPatrones; ?></div>
        <span class="stat-pill stat-pill-gray">Total en sistema</span>
      </div>
    </div>

    <!-- Usuarios recientes -->
    <div class="projects-box" style="margin-top: 2rem;">
      <div class="projects-top d-flex justify-content-between align-items-center">
        <div>
          <h2 class="mb-1">Usuarios Recientes</h2>
          <span style="font-size: 0.85rem; color: #a5b4fc;"><i class="bi bi-clock-history me-1"></i> Sincronizado en tiempo real con MySQL</span>
        </div>
        <a href="/FromIA-HTML/dashboard.php?seccion=usuarios" class="btn btn-sm" style="background: rgba(124, 58, 237, 0.2); color: #c4b5fd; border: 1px solid rgba(124, 58, 237, 0.4); border-radius: 8px;">
          Ver todos los usuarios <i class="bi bi-arrow-right ms-1"></i>
        </a>
      </div>

      <div class="table-responsive mt-3">
        <table class="table table-dark table-hover mb-0 align-middle" style="background: transparent;">
          <thead>
            <tr style="color: #888; font-size: 0.75rem; text-transform: uppercase; border-bottom: 1px solid rgba(255,255,255,0.1);">
              <th>Usuario</th>
              <th>Correo</th>
              <th>Rol</th>
              <th>Plan Activo</th>
              <th>Estado</th>
            </tr>
          </thead>
          <tbody style="font-size: 0.88rem;">
            <?php if (!empty($usuariosRecientes)): ?>
              <?php foreach ($usuariosRecientes as $u): ?>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                  <td class="fw-semibold text-white">
                    <i class="bi bi-person-circle me-2 text-info"></i>
                    <?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido'], ENT_QUOTES, 'UTF-8'); ?>
                  </td>
                  <td class="text-secondary"><?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                  <td>
                    <span class="badge bg-secondary"><?= htmlspecialchars(ucfirst($u['nomb_rol'] ?? 'cliente'), ENT_QUOTES, 'UTF-8'); ?></span>
                  </td>
                  <td>
                    <span class="badge bg-primary"><?= htmlspecialchars($u['nombre_plan'] ?? 'Sin Plan', ENT_QUOTES, 'UTF-8'); ?></span>
                  </td>
                  <td>
                    <?php if (($u['estado_usr'] ?? '') === 'activo'): ?>
                      <span class="stat-pill stat-pill-green">Activo</span>
                    <?php else: ?>
                      <span class="stat-pill stat-pill-gray"><?= htmlspecialchars(ucfirst($u['estado_usr'] ?? 'Inactivo'), ENT_QUOTES, 'UTF-8'); ?></span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" class="text-center text-muted py-4">No hay usuarios registrados aún.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>