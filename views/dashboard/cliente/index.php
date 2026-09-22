<?php
$pageTitle     = "FormAI - Mi Espacio";
$extraCss      = "dashboard.css";
$nombre        = $nombre ?? ($_SESSION['user_nombre'] ?? 'Cliente');
$totalActivos  = $totalActivos ?? (isset($proyectos) && is_array($proyectos) ? count($proyectos) : 0);
$suscripcion   = $suscripcion ?? null;
$diasRestantes = $diasRestantes ?? 0;

require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="dashboard-wrapper">
  <!-- Sidebar Cliente -->
  <aside class="sidebar-custom">
    <div>
      <div class="sidebar-group-title">PRINCIPAL</div>
      <ul class="sidebar-menu">
        <li><a href="/FromIA-HTML/dashboard.php" class="active"><i class="bi bi-house-door-fill"></i> Inicio</a></li>
        <li><a href="#"><i class="bi bi-download"></i> Descargar App <span class="sidebar-badge-red">NUEVO</span></a></li>
        <li><a href="#"><i class="bi bi-display"></i> Prueba Web Gratuita</a></li>
      </ul>
    </div>
    <div>
      <div class="sidebar-group-title">MI CUENTA</div>
      <ul class="sidebar-menu">
        <li><a href="/FromIA-HTML/dashboard.php?seccion=perfil"><i class="bi bi-person-fill"></i> Mi Perfil</a></li>
        <li><a href="/FromIA-HTML/dashboard.php?seccion=membresia"><i class="bi bi-gem"></i> Mi Membresía</a></li>
        <li><a href="/FromIA-HTML/dashboard.php?seccion=proyectos"><i class="bi bi-folder-fill"></i> Mis Proyectos <span class="sidebar-badge-count"><?= (int)$totalActivos; ?></span></a></li>
      </ul>
    </div>
    <div>
      <div class="sidebar-group-title">SOPORTE</div>
      <ul class="sidebar-menu">
        <li><a href="#"><i class="bi bi-bug-fill"></i> Reportar Problema</a></li>
        <li><a href="#"><i class="bi bi-question-circle-fill"></i> Soporte</a></li>
        <li><a href="/FromIA-HTML/index.php" style="margin-top: 10px;"><i class="bi bi-globe"></i> Ir al sitio web</a></li>
      </ul>
    </div>
  </aside>

  <!-- Contenido Cliente -->
  <main class="main-panel">
    <h1 class="welcome-title">Bienvenido, <?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8'); ?> 👋</h1>
    <p class="welcome-subtitle">
      <?php if (!empty($suscripcion)): ?>
        <?php if (($suscripcion['estado_real'] ?? '') === 'activa'): ?>
          Plan <strong><?= htmlspecialchars($suscripcion['nombre_plan'], ENT_QUOTES, 'UTF-8'); ?></strong> activo · Próxima factura: <?= date('d/m/Y', strtotime($suscripcion['fecha_fin'])); ?>
        <?php elseif (($suscripcion['estado_real'] ?? '') === 'vencida'): ?>
          Plan <strong><?= htmlspecialchars($suscripcion['nombre_plan'], ENT_QUOTES, 'UTF-8'); ?></strong> <span style="color: #ff6b6b; font-weight: 600;">expirado el <?= date('d/m/Y', strtotime($suscripcion['fecha_fin'])); ?></span> · <a href="/FromIA-HTML/dashboard.php?seccion=membresia" style="color: #2ecc71; text-decoration: underline; font-weight: 600;">Renovar ahora</a>
        <?php else: ?>
          Plan <strong><?= htmlspecialchars($suscripcion['nombre_plan'], ENT_QUOTES, 'UTF-8'); ?></strong> (<?= ucfirst(htmlspecialchars($suscripcion['estado_real'])); ?>)
        <?php endif; ?>
      <?php else: ?>
        Sin membresía activa · <a href="/FromIA-HTML/dashboard.php?seccion=membresia" style="color: #2ecc71; text-decoration: underline; font-weight: 600;">Adquirir un Plan</a>
      <?php endif; ?>
    </p>

    <!-- Métricas del Cliente -->
    <div class="stats-grid">
      <div class="stat-card-custom">
        <div class="stat-header">
          <span class="stat-header-label">PROYECTOS ACTIVOS</span>
          <i class="bi bi-folder stat-header-icon"></i>
        </div>
        <div class="stat-number"><?= (int)$totalActivos; ?></div>
        <span class="stat-pill stat-pill-green">En tu cuenta</span>
      </div>

      <div class="stat-card-custom">
        <div class="stat-header">
          <span class="stat-header-label">PLAN CONTRATADO</span>
          <i class="bi bi-gem stat-header-icon"></i>
        </div>
        <div class="stat-number" style="font-size: 1.6rem; font-weight: 700; padding-top: 8px;">
          <?= !empty($suscripcion) ? htmlspecialchars($suscripcion['nombre_plan'], ENT_QUOTES, 'UTF-8') : 'Gratuito'; ?>
        </div>
        <?php if (!empty($suscripcion) && ($suscripcion['estado_real'] ?? '') === 'activa'): ?>
          <span class="stat-pill stat-pill-green">
            Activo (<?= htmlspecialchars($suscripcion['ciclo']); ?>)
          </span>
        <?php elseif (!empty($suscripcion) && ($suscripcion['estado_real'] ?? '') === 'vencida'): ?>
          <span class="stat-pill" style="background: rgba(231, 76, 60, 0.2); color: #ff6b6b; border: 1px solid rgba(231, 76, 60, 0.4);">
            Vencido
          </span>
        <?php else: ?>
          <span class="stat-pill stat-pill-gray">Básico</span>
        <?php endif; ?>
      </div>

      <div class="stat-card-custom">
        <div class="stat-header">
          <span class="stat-header-label">DÍAS RESTANTES</span>
          <i class="bi bi-clock stat-header-icon"></i>
        </div>
        <div class="stat-number"><?= (int)$diasRestantes; ?></div>
        <?php if (!empty($suscripcion) && ($suscripcion['estado_real'] ?? '') === 'activa'): ?>
          <span class="stat-pill stat-pill-green">del periodo</span>
        <?php elseif (!empty($suscripcion) && ($suscripcion['estado_real'] ?? '') === 'vencida'): ?>
          <span class="stat-pill" style="background: rgba(231, 76, 60, 0.2); color: #ff6b6b;">Expirado</span>
        <?php else: ?>
          <span class="stat-pill stat-pill-gray">sin periodo</span>
        <?php endif; ?>
      </div>
    </div>

    <!-- Proyectos recientes -->
    <div class="projects-box">
      <div class="projects-top">
        <h2>Proyectos recientes</h2>
        <div class="d-flex align-items-center gap-3">
          <a href="/FromIA-HTML/dashboard.php?seccion=proyectos" class="btn-formai-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Nuevo Proyecto
          </a>
          <a href="/FromIA-HTML/dashboard.php?seccion=proyectos">Ver todos &rarr;</a>
        </div>
      </div>

      <div class="projects-table-header">
        <span>PROYECTO</span>
        <span>TIPO</span>
        <span>MODIFICADO</span>
        <span style="text-align: right;">ESTADO</span>
      </div>

      <?php if (!empty($proyectos)): ?>
        <?php foreach (array_slice($proyectos, 0, 5) as $p): ?>
          <div class="project-row">
            <div>
              <span class="text-white fw-semibold">
                <i class="bi bi-folder-fill me-2 text-warning"></i>
                <?= htmlspecialchars($p['nomb_patron'], ENT_QUOTES, 'UTF-8'); ?>
              </span>
              <?php if (!empty($p['desc_patron'])): ?>
                <div style="font-size: 0.78rem; color: #8a87a2; margin-left: 26px;">
                  <?= htmlspecialchars($p['desc_patron'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
              <?php endif; ?>
            </div>
            <div>
              <span class="badge bg-secondary" style="font-size: 0.75rem; text-transform: capitalize;">
                <?= htmlspecialchars($p['tipo_patron'], ENT_QUOTES, 'UTF-8'); ?>
              </span>
            </div>
            <div style="color: #8c89a4; font-size: 0.82rem;">
              <?= date('d/m/Y', strtotime($p['fecha_creacion'])); ?>
            </div>
            <div style="text-align: right;">
              <span class="stat-pill stat-pill-green" style="text-transform: capitalize;">
                <?= htmlspecialchars($p['estado_patron'], ENT_QUOTES, 'UTF-8'); ?>
              </span>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="empty-projects-state">
          <i class="bi bi-folder2-open fs-2 d-block mb-2 text-secondary"></i>
          No tienes proyectos aún en tu cuenta.<br>
          <a href="/FromIA-HTML/dashboard.php?seccion=proyectos" class="btn-formai-primary btn-sm mt-3">
            <i class="bi bi-plus-lg me-1"></i> Crear mi primer proyecto
          </a>
        </div>
      <?php endif; ?>
    </div>
  </main>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>