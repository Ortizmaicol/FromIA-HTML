<?php
$pageTitle         = "FormAI - Usuarios del Sistema";
$extraCss          = "dashboard.css";
$nombre            = $nombre ?? ($_SESSION['user_nombre'] ?? 'Administrador');
$usuarios          = $usuarios ?? [];
$totalUsuarios     = $totalUsuarios ?? 0;
$activosCount      = $activosCount ?? 0;
$suspendidosCount  = $suspendidosCount ?? 0;
$eliminadosCount   = $eliminadosCount ?? 0;
$sesionesActivas   = $sesionesActivas ?? [];
$busqueda          = $busqueda ?? '';
$filtroRol         = $filtroRol ?? '';
$filtroEst         = $filtroEst ?? '';

require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="dashboard-wrapper">
  <!-- Sidebar Administrador -->
  <aside class="sidebar-custom">
    <div>
      <div class="sidebar-group-title">GESTIÓN GENERAL</div>
      <ul class="sidebar-menu">
        <li><a href="/FromIA-HTML/dashboard.php"><i class="bi bi-speedometer2"></i> Métricas Globales</a></li>
        <li><a href="/FromIA-HTML/dashboard.php?seccion=usuarios" class="active"><i class="bi bi-people-fill"></i> Usuarios del Sistema</a></li>
        <li><a href="/FromIA-HTML/dashboard.php?seccion=pagos"><i class="bi bi-credit-card-fill"></i> Historial de Pagos</a></li>
        <li><a href="/FromIA-HTML/dashboard.php?seccion=reportes"><i class="bi bi-file-earmark-bar-graph"></i> Reportes</a></li>
      </ul>
    </div>
  </aside>

  <!-- Contenido Principal -->
  <main class="main-panel">
    <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
      <div>
        <h1 class="welcome-title mb-1">Usuarios del Sistema 👥</h1>
        <p class="welcome-subtitle">Datos en tiempo real consultados mediante la vista de base de datos <code>v_usuarios</code></p>
      </div>
      <div class="d-flex align-items-center gap-2">
        <span class="badge" style="background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); padding: 8px 14px; font-weight: 500;">
          <i class="bi bi-broadcast me-1"></i> Sincronizado en Vivo
        </span>
      </div>
    </div>

    <!-- Mensajes de feedback -->
    <?php if (!empty($mensajeExito)): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert" style="background: rgba(34, 197, 94, 0.15); border: 1px solid rgba(34, 197, 94, 0.4); color: #86efac; border-radius: 12px;">
        <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($mensajeExito, ENT_QUOTES, 'UTF-8'); ?>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <?php if (!empty($mensajeError)): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.4); color: #fca5a5; border-radius: 12px;">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($mensajeError, ENT_QUOTES, 'UTF-8'); ?>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <!-- Métricas Rápidas -->
    <div class="stats-grid mb-4">
      <div class="stat-card-custom">
        <div class="stat-header">
          <span class="stat-header-label">TOTAL REGISTRADOS</span>
          <i class="bi bi-people stat-header-icon"></i>
        </div>
        <div class="stat-number"><?= (int)$totalUsuarios; ?></div>
        <span class="stat-pill stat-pill-gray">En tabla MySQL</span>
      </div>

      <div class="stat-card-custom">
        <div class="stat-header">
          <span class="stat-header-label">USUARIOS ACTIVOS</span>
          <i class="bi bi-person-check-fill stat-header-icon text-success"></i>
        </div>
        <div class="stat-number text-success"><?= (int)$activosCount; ?></div>
        <span class="stat-pill stat-pill-green">Operativos</span>
      </div>

      <div class="stat-card-custom">
        <div class="stat-header">
          <span class="stat-header-label">SUSPENDIDOS</span>
          <i class="bi bi-person-x stat-header-icon text-warning"></i>
        </div>
        <div class="stat-number text-warning"><?= (int)$suspendidosCount; ?></div>
        <span class="stat-pill" style="background: rgba(234, 179, 8, 0.15); color: #facc15; border: 1px solid rgba(234, 179, 8, 0.3);">Acceso pausado</span>
      </div>

      <div class="stat-card-custom">
        <div class="stat-header">
          <span class="stat-header-label">DADOS DE BAJA</span>
          <i class="bi bi-shield-x stat-header-icon text-danger"></i>
        </div>
        <div class="stat-number text-danger"><?= (int)$eliminadosCount; ?></div>
        <span class="stat-pill" style="background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3);">sp_dar_baja_usuario</span>
      </div>
    </div>

    <!-- Barra de Filtros y Búsqueda -->
    <div class="projects-box mb-4 p-3" style="background: rgba(25, 17, 50, 0.7); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 14px;">
      <form method="GET" action="/FromIA-HTML/dashboard.php" class="row g-3 align-items-center">
        <input type="hidden" name="seccion" value="usuarios">
        
        <div class="col-md-5 col-12">
          <div class="input-group">
            <span class="input-group-text" style="background: #1e153b; border-color: rgba(255,255,255,0.12); color: #94a3b8;">
              <i class="bi bi-search"></i>
            </span>
            <input type="text" name="q" class="form-control" placeholder="Buscar por nombre, apellido o correo..." 
                   value="<?= htmlspecialchars($busqueda, ENT_QUOTES, 'UTF-8'); ?>"
                   style="background: #1e153b; border-color: rgba(255,255,255,0.12); color: #fff;">
          </div>
        </div>

        <div class="col-md-3 col-6">
          <select name="rol" class="form-select" style="background: #1e153b; border-color: rgba(255,255,255,0.12); color: #fff;">
            <option value="">Todos los Roles</option>
            <option value="administrador" <?= $filtroRol === 'administrador' ? 'selected' : ''; ?>>Administrador</option>
            <option value="cliente" <?= $filtroRol === 'cliente' ? 'selected' : ''; ?>>Cliente</option>
          </select>
        </div>

        <div class="col-md-2 col-6">
          <select name="estado" class="form-select" style="background: #1e153b; border-color: rgba(255,255,255,0.12); color: #fff;">
            <option value="">Todos los Estados</option>
            <option value="activo" <?= $filtroEst === 'activo' ? 'selected' : ''; ?>>Activo</option>
            <option value="inactivo" <?= $filtroEst === 'inactivo' ? 'selected' : ''; ?>>Inactivo</option>
            <option value="suspendido" <?= $filtroEst === 'suspendido' ? 'selected' : ''; ?>>Suspendido</option>
            <option value="eliminado" <?= $filtroEst === 'eliminado' ? 'selected' : ''; ?>>Eliminado</option>
          </select>
        </div>

        <div class="col-md-2 col-12 d-flex gap-2">
          <button type="submit" class="btn btn-primary w-100" style="background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%); border: none;">
            <i class="bi bi-funnel-fill me-1"></i> Filtrar
          </button>
          <?php if (!empty($busqueda) || !empty($filtroRol) || !empty($filtroEst)): ?>
            <a href="/FromIA-HTML/dashboard.php?seccion=usuarios" class="btn btn-outline-secondary" title="Limpiar filtros" style="border-color: rgba(255,255,255,0.2); color: #ccc;">
              <i class="bi bi-x-lg"></i>
            </a>
          <?php endif; ?>
        </div>
      </form>
    </div>

    <!-- Tabla Principal de Usuarios -->
    <div class="projects-box">
      <div class="projects-top d-flex justify-content-between align-items-center">
        <div>
          <h2 class="mb-1">Directorio de Usuarios</h2>
          <span style="font-size: 0.85rem; color: #a5b4fc;">Mostrando <?= count($usuarios); ?> registro(s) coincidentes</span>
        </div>
      </div>

      <div class="table-responsive mt-3">
        <table class="table table-dark table-hover mb-0 align-middle" style="background: transparent;">
          <thead>
            <tr style="color: #94a3b8; font-size: 0.75rem; text-transform: uppercase; border-bottom: 1px solid rgba(255,255,255,0.1);">
              <th>ID</th>
              <th>Usuario</th>
              <th>Correo Electrónico</th>
              <th>Rol</th>
              <th>Plan de Suscripción</th>
              <th>Estado</th>
              <th class="text-end">Acciones Administrativas</th>
            </tr>
          </thead>
          <tbody style="font-size: 0.88rem;">
            <?php if (!empty($usuarios)): ?>
              <?php foreach ($usuarios as $u): ?>
                <?php
                  $idU = (int)$u['id_usuario'];
                  $esPropioUsuario = ($idU === (int)($_SESSION['user_id'] ?? 0));
                  $estadoU = $u['estado_usr'] ?? 'activo';
                  $rolU = strtolower($u['nomb_rol'] ?? 'cliente');
                ?>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                  <td class="text-secondary">#<?= $idU; ?></td>
                  <td class="fw-semibold text-white">
                    <div class="d-flex align-items-center">
                      <div class="rounded-circle d-flex align-items-center justify-content-center me-2" 
                           style="width: 34px; height: 34px; background: rgba(124, 58, 237, 0.2); color: #a78bfa; font-weight: bold; border: 1px solid rgba(124, 58, 237, 0.4);">
                        <?= strtoupper(substr($u['nombre'], 0, 1)); ?>
                      </div>
                      <div>
                        <div><?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php if ($esPropioUsuario): ?>
                          <span class="badge" style="background: rgba(59, 130, 246, 0.2); color: #93c5fd; font-size: 0.7rem;">Tu Cuenta</span>
                        <?php endif; ?>
                      </div>
                    </div>
                  </td>
                  <td class="text-secondary"><?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                  <td>
                    <?php if ($rolU === 'administrador'): ?>
                      <span class="badge" style="background: rgba(168, 85, 247, 0.25); color: #e9d5ff; border: 1px solid rgba(168, 85, 247, 0.4);">
                        <i class="bi bi-shield-lock-fill me-1"></i> Administrador
                      </span>
                    <?php else: ?>
                      <span class="badge bg-secondary" style="border: 1px solid rgba(255,255,255,0.15);">
                        <i class="bi bi-person me-1"></i> Cliente
                      </span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if (!empty($u['nombre_plan'])): ?>
                      <span class="badge" style="background: rgba(14, 165, 233, 0.2); color: #7dd3fc; border: 1px solid rgba(14, 165, 233, 0.4);">
                        <i class="bi bi-gem me-1"></i> <?= htmlspecialchars($u['nombre_plan'], ENT_QUOTES, 'UTF-8'); ?>
                      </span>
                      <?php if (!empty($u['suscripcion_vence'])): ?>
                        <div style="font-size: 0.72rem; color: #94a3b8; margin-top: 3px;">
                          Vence: <?= htmlspecialchars($u['suscripcion_vence'], ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                      <?php endif; ?>
                    <?php else: ?>
                      <span class="text-muted" style="font-size: 0.8rem;">Sin membresía</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if ($estadoU === 'activo'): ?>
                      <span class="stat-pill stat-pill-green">Activo</span>
                    <?php elseif ($estadoU === 'suspendido'): ?>
                      <span class="stat-pill" style="background: rgba(234, 179, 8, 0.15); color: #facc15; border: 1px solid rgba(234, 179, 8, 0.3);">Suspendido</span>
                    <?php elseif ($estadoU === 'eliminado'): ?>
                      <span class="stat-pill" style="background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3);">Dado de baja</span>
                    <?php else: ?>
                      <span class="stat-pill stat-pill-gray"><?= htmlspecialchars(ucfirst($estadoU), ENT_QUOTES, 'UTF-8'); ?></span>
                    <?php endif; ?>
                  </td>
                  <td class="text-end">
                    <?php if (!$esPropioUsuario): ?>
                      <div class="dropdown d-inline-block">
                        <button class="btn btn-sm btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" 
                                style="border-color: rgba(255,255,255,0.2); font-size: 0.8rem; border-radius: 8px;">
                          Gestionar
                        </button>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end" style="background: #191132; border: 1px solid rgba(255,255,255,0.15); border-radius: 10px; font-size: 0.85rem;">
                          <!-- Cambiar Rol -->
                          <li>
                            <form method="POST" action="/FromIA-HTML/dashboard.php?seccion=usuarios">
                              <input type="hidden" name="accion" value="cambiar_rol">
                              <input type="hidden" name="id_usuario" value="<?= $idU; ?>">
                              <input type="hidden" name="id_rol" value="<?= $rolU === 'administrador' ? 2 : 1; ?>">
                              <button type="submit" class="dropdown-item py-2">
                                <i class="bi bi-arrow-left-right me-2 text-info"></i>
                                Cambiar a <?= $rolU === 'administrador' ? 'Cliente' : 'Administrador'; ?>
                              </button>
                            </form>
                          </li>

                          <!-- Cambiar Estado -->
                          <?php if ($estadoU !== 'activo'): ?>
                            <li>
                              <form method="POST" action="/FromIA-HTML/dashboard.php?seccion=usuarios">
                                <input type="hidden" name="accion" value="cambiar_estado_usuario">
                                <input type="hidden" name="id_usuario" value="<?= $idU; ?>">
                                <input type="hidden" name="nuevo_estado" value="activo">
                                <button type="submit" class="dropdown-item py-2 text-success">
                                  <i class="bi bi-check-circle me-2"></i> Reactivar Cuenta
                                </button>
                              </form>
                            </li>
                          <?php endif; ?>

                          <?php if ($estadoU !== 'suspendido' && $estadoU !== 'eliminado'): ?>
                            <li>
                              <form method="POST" action="/FromIA-HTML/dashboard.php?seccion=usuarios">
                                <input type="hidden" name="accion" value="cambiar_estado_usuario">
                                <input type="hidden" name="id_usuario" value="<?= $idU; ?>">
                                <input type="hidden" name="nuevo_estado" value="suspendido">
                                <button type="submit" class="dropdown-item py-2 text-warning">
                                  <i class="bi bi-pause-circle me-2"></i> Suspender Cuenta
                                </button>
                              </form>
                            </li>
                          <?php endif; ?>

                          <li><hr class="dropdown-divider" style="border-color: rgba(255,255,255,0.1);"></li>

                          <!-- Dar de baja mediante Procedimiento Almacenado -->
                          <?php if ($estadoU !== 'eliminado'): ?>
                            <li>
                              <form method="POST" action="/FromIA-HTML/dashboard.php?seccion=usuarios" 
                                    onsubmit="return confirm('¿Estás seguro de que deseas dar de baja a <?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido'], ENT_QUOTES, 'UTF-8'); ?>? Esta acción ejecutará el procedimiento sp_dar_baja_usuario, cancelará sus suscripciones y revocará todas sus sesiones activas.');">
                                <input type="hidden" name="accion" value="dar_baja_usuario">
                                <input type="hidden" name="id_usuario" value="<?= $idU; ?>">
                                <button type="submit" class="dropdown-item py-2 text-danger">
                                  <i class="bi bi-trash3 me-2"></i> Dar de Baja (sp_dar_baja_usuario)
                                </button>
                              </form>
                            </li>
                          <?php else: ?>
                            <li>
                              <span class="dropdown-item text-muted disabled py-2">
                                <i class="bi bi-x-circle me-2"></i> Usuario ya dado de baja
                              </span>
                            </li>
                          <?php endif; ?>
                        </ul>
                      </div>
                    <?php else: ?>
                      <span class="text-muted" style="font-size: 0.8rem;">Sesión actual</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" class="text-center text-muted py-5">
                  <i class="bi bi-people mb-2 d-block" style="font-size: 2rem; opacity: 0.4;"></i>
                  No se encontraron usuarios con los criterios de búsqueda seleccionados.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
