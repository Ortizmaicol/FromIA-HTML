<?php
$pageTitle       = "FormAI - Historial de Pagos";
$extraCss        = "dashboard.css";
$nombre          = $nombre ?? ($_SESSION['user_nombre'] ?? 'Administrador');
$historialPagos  = $historialPagos ?? [];
$resumenClientes = $resumenClientes ?? [];
$totalIngresos   = $totalIngresos ?? 0.0;
$totalPagos      = $totalPagos ?? 0;
$montoPromedio   = $montoPromedio ?? 0.0;

require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="dashboard-wrapper">
  <!-- Sidebar Administrador -->
  <aside class="sidebar-custom">
    <div>
      <div class="sidebar-group-title">GESTIÓN GENERAL</div>
      <ul class="sidebar-menu">
        <li><a href="/FromIA-HTML/dashboard.php"><i class="bi bi-speedometer2"></i> Métricas Globales</a></li>
        <li><a href="/FromIA-HTML/dashboard.php?seccion=usuarios"><i class="bi bi-people-fill"></i> Usuarios del Sistema</a></li>
        <li><a href="/FromIA-HTML/dashboard.php?seccion=pagos" class="active"><i class="bi bi-credit-card-fill"></i> Historial de Pagos</a></li>
        <li><a href="/FromIA-HTML/dashboard.php?seccion=reportes"><i class="bi bi-file-earmark-bar-graph"></i> Reportes</a></li>
      </ul>
    </div>
  </aside>

  <!-- Contenido Principal -->
  <main class="main-panel">
    <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
      <div>
        <h1 class="welcome-title mb-1">Historial de Pagos 💳</h1>
        <p class="welcome-subtitle">Registro de transacciones reales y agregación financiera mediante <code>v_resumen_pagos</code></p>
      </div>
      <div class="d-flex align-items-center gap-2">
        <a href="/FromIA-HTML/dashboard.php?seccion=reportes" class="btn btn-sm" style="background: rgba(124, 58, 237, 0.25); color: #c4b5fd; border: 1px solid rgba(124, 58, 237, 0.4); border-radius: 8px;">
          <i class="bi bi-graph-up me-1"></i> Ver Reporte Financiero
        </a>
      </div>
    </div>

    <!-- Métricas Financieras -->
    <div class="stats-grid mb-4">
      <div class="stat-card-custom">
        <div class="stat-header">
          <span class="stat-header-label">TOTAL RECAUDADO</span>
          <i class="bi bi-cash-stack stat-header-icon text-success"></i>
        </div>
        <div class="stat-number text-success">$<?= number_format((float)$totalIngresos, 2); ?></div>
        <span class="stat-pill stat-pill-green">Ingresos USD</span>
      </div>

      <div class="stat-card-custom">
        <div class="stat-header">
          <span class="stat-header-label">TOTAL TRANSACCIONES</span>
          <i class="bi bi-receipt-cutoff stat-header-icon"></i>
        </div>
        <div class="stat-number"><?= (int)$totalPagos; ?></div>
        <span class="stat-pill stat-pill-gray">Registros en tabla</span>
      </div>

      <div class="stat-card-custom">
        <div class="stat-header">
          <span class="stat-header-label">TICKET PROMEDIO</span>
          <i class="bi bi-calculator stat-header-icon text-info"></i>
        </div>
        <div class="stat-number text-info">$<?= number_format((float)$montoPromedio, 2); ?></div>
        <span class="stat-pill" style="background: rgba(14, 165, 233, 0.15); color: #7dd3fc; border: 1px solid rgba(14, 165, 233, 0.3);">Por transacción</span>
      </div>

      <div class="stat-card-custom">
        <div class="stat-header">
          <span class="stat-header-label">CLIENTES COMPRADORES</span>
          <i class="bi bi-people stat-header-icon text-warning"></i>
        </div>
        <div class="stat-number text-warning"><?= count($resumenClientes); ?></div>
        <span class="stat-pill" style="background: rgba(234, 179, 8, 0.15); color: #facc15; border: 1px solid rgba(234, 179, 8, 0.3);">v_resumen_pagos</span>
      </div>
    </div>

    <!-- Pestañas de navegación -->
    <ul class="nav nav-pills mb-3" id="pagosTabs" role="tablist" style="gap: 8px;">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="todas-tab" data-bs-toggle="pill" data-bs-target="#todas-transacciones" type="button" role="tab"
                style="background: #191132; color: #fff; border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; padding: 8px 18px;">
          <i class="bi bi-list-ul me-1"></i> Todas las Transacciones (<?= count($historialPagos); ?>)
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="resumen-tab" data-bs-toggle="pill" data-bs-target="#resumen-clientes" type="button" role="tab"
                style="background: #191132; color: #cbd5e1; border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; padding: 8px 18px;">
          <i class="bi bi-person-lines-fill me-1"></i> Resumen por Cliente (Vista v_resumen_pagos)
        </button>
      </li>
    </ul>

    <div class="tab-content" id="pagosTabsContent">
      <!-- Pestaña 1: Todas las Transacciones -->
      <div class="tab-pane fade show active" id="todas-transacciones" role="tabpanel">
        <div class="projects-box">
          <div class="projects-top d-flex justify-content-between align-items-center">
            <div>
              <h2 class="mb-1">Transacciones Registradas</h2>
              <span style="font-size: 0.85rem; color: #a5b4fc;">Histórico detallado con método de pago y referencia</span>
            </div>
          </div>

          <div class="table-responsive mt-3">
            <table class="table table-dark table-hover mb-0 align-middle" style="background: transparent;">
              <thead>
                <tr style="color: #94a3b8; font-size: 0.75rem; text-transform: uppercase; border-bottom: 1px solid rgba(255,255,255,0.1);">
                  <th>#ID</th>
                  <th>Cliente</th>
                  <th>Plan Suscrito</th>
                  <th>Monto</th>
                  <th>Método</th>
                  <th>Nro. Transacción</th>
                  <th>Fecha y Hora</th>
                  <th>Estado</th>
                </tr>
              </thead>
              <tbody style="font-size: 0.88rem;">
                <?php if (!empty($historialPagos)): ?>
                  <?php foreach ($historialPagos as $p): ?>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                      <td class="text-secondary fw-bold">#<?= (int)$p['id_pago']; ?></td>
                      <td>
                        <div class="fw-semibold text-white">
                          <?= htmlspecialchars($p['nombre'] . ' ' . $p['apellido'], ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                        <div class="text-secondary" style="font-size: 0.78rem;">
                          <?= htmlspecialchars($p['email'], ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                      </td>
                      <td>
                        <span class="badge" style="background: rgba(124, 58, 237, 0.2); color: #c4b5fd; border: 1px solid rgba(124, 58, 237, 0.4);">
                          <?= htmlspecialchars($p['nombre_plan'] ?? 'Plan FormAI', ENT_QUOTES, 'UTF-8'); ?>
                        </span>
                      </td>
                      <td class="fw-bold text-success" style="font-size: 0.95rem;">
                        $<?= number_format((float)$p['monto'], 2); ?> <span style="font-size: 0.75rem; color: #94a3b8; font-weight: normal;"><?= htmlspecialchars($p['moneda'] ?? 'USD', ENT_QUOTES, 'UTF-8'); ?></span>
                      </td>
                      <td>
                        <?php
                          $metodo = strtolower($p['metodo'] ?? 'tarjeta');
                          if ($metodo === 'tarjeta'):
                        ?>
                          <span class="badge" style="background: rgba(59, 130, 246, 0.2); color: #93c5fd; border: 1px solid rgba(59, 130, 246, 0.3);">
                            <i class="bi bi-credit-card-2-front me-1"></i> Tarjeta
                          </span>
                        <?php elseif ($metodo === 'paypal'): ?>
                          <span class="badge" style="background: rgba(14, 165, 233, 0.2); color: #38bdf8; border: 1px solid rgba(14, 165, 233, 0.3);">
                            <i class="bi bi-paypal me-1"></i> PayPal
                          </span>
                        <?php else: ?>
                          <span class="badge bg-secondary">
                            <i class="bi bi-cash me-1"></i> <?= htmlspecialchars(ucfirst($metodo), ENT_QUOTES, 'UTF-8'); ?>
                          </span>
                        <?php endif; ?>
                      </td>
                      <td class="text-secondary" style="font-family: monospace; font-size: 0.8rem;">
                        <?= !empty($p['nro_transaccion']) ? htmlspecialchars($p['nro_transaccion'], ENT_QUOTES, 'UTF-8') : '<span class="text-muted">N/D</span>'; ?>
                      </td>
                      <td class="text-secondary" style="font-size: 0.82rem;">
                        <?= htmlspecialchars(date('d/m/Y H:i', strtotime($p['creado_en'])), ENT_QUOTES, 'UTF-8'); ?>
                      </td>
                      <td>
                        <?php if ($p['estado'] === 'completado'): ?>
                          <span class="stat-pill stat-pill-green">Completado</span>
                        <?php elseif ($p['estado'] === 'pendiente'): ?>
                          <span class="stat-pill" style="background: rgba(234, 179, 8, 0.15); color: #facc15; border: 1px solid rgba(234, 179, 8, 0.3);">Pendiente</span>
                        <?php elseif ($p['estado'] === 'reembolsado'): ?>
                          <span class="stat-pill" style="background: rgba(148, 163, 184, 0.15); color: #cbd5e1; border: 1px solid rgba(148, 163, 184, 0.3);">Reembolsado</span>
                        <?php else: ?>
                          <span class="stat-pill" style="background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3);"><?= htmlspecialchars(ucfirst($p['estado']), ENT_QUOTES, 'UTF-8'); ?></span>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="8" class="text-center text-muted py-5">
                      <i class="bi bi-receipt mb-2 d-block" style="font-size: 2rem; opacity: 0.4;"></i>
                      No hay transacciones de pago registradas en la base de datos.
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Pestaña 2: Resumen por Cliente (Vista v_resumen_pagos) -->
      <div class="tab-pane fade" id="resumen-clientes" role="tabpanel">
        <div class="projects-box">
          <div class="projects-top d-flex justify-content-between align-items-center">
            <div>
              <h2 class="mb-1">Resumen Agregado por Cliente</h2>
              <span style="font-size: 0.85rem; color: #a5b4fc;">Generado automáticamente por la vista de MySQL <code>v_resumen_pagos</code></span>
            </div>
          </div>

          <div class="table-responsive mt-3">
            <table class="table table-dark table-hover mb-0 align-middle" style="background: transparent;">
              <thead>
                <tr style="color: #94a3b8; font-size: 0.75rem; text-transform: uppercase; border-bottom: 1px solid rgba(255,255,255,0.1);">
                  <th>ID Cliente</th>
                  <th>Cliente</th>
                  <th>Total de Pagos</th>
                  <th>Total Acumulado Pagado</th>
                  <th>Último Pago Realizado</th>
                </tr>
              </thead>
              <tbody style="font-size: 0.88rem;">
                <?php if (!empty($resumenClientes)): ?>
                  <?php foreach ($resumenClientes as $rc): ?>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                      <td class="text-secondary fw-semibold">#<?= (int)$rc['id_usuario']; ?></td>
                      <td class="fw-semibold text-white">
                        <div class="d-flex align-items-center">
                          <i class="bi bi-person-circle me-2 text-info" style="font-size: 1.2rem;"></i>
                          <?= htmlspecialchars($rc['nombre'] . ' ' . $rc['apellido'], ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                      </td>
                      <td>
                        <span class="badge" style="background: rgba(99, 102, 241, 0.2); color: #a5b4fc; border: 1px solid rgba(99, 102, 241, 0.3); font-size: 0.82rem;">
                          <?= (int)$rc['total_pagos']; ?> pago(s)
                        </span>
                      </td>
                      <td class="fw-bold text-success" style="font-size: 0.95rem;">
                        $<?= number_format((float)$rc['total_pagado'], 2); ?> USD
                      </td>
                      <td class="text-secondary" style="font-size: 0.82rem;">
                        <?= !empty($rc['ultimo_pago']) ? htmlspecialchars(date('d/m/Y H:i', strtotime($rc['ultimo_pago'])), ENT_QUOTES, 'UTF-8') : '<span class="text-muted">Sin pagos</span>'; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="5" class="text-center text-muted py-5">
                      No se encontraron registros de clientes con pagos en <code>v_resumen_pagos</code>.
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
