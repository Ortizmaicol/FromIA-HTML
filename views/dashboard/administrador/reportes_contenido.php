<?php
/**
 * Vista Parcial: Contenido exportable del reporte
 * Utilizado tanto para la carga inicial como para actualizaciones en tiempo real vía AJAX
 */
?>
<!-- Encabezado Institucional del Reporte -->
<div class="p-3 mb-4 rounded-3 d-flex justify-content-between align-items-center flex-wrap gap-2" 
     style="background: linear-gradient(135deg, rgba(30, 20, 61, 0.9) 0%, rgba(15, 10, 33, 0.9) 100%); border: 1px solid rgba(124, 58, 237, 0.35);">
  <div>
    <h3 class="fw-bold mb-1" style="color: #ffffff; letter-spacing: 0.5px;">
      <i class="bi bi-file-earmark-bar-graph-fill me-2 text-primary"></i>
      FormAI &bull; <?= $tipoReporte === 'ingresos' ? 'Reporte Financiero de Ingresos y Transacciones' : ($tipoReporte === 'usuarios' ? 'Reporte General de Usuarios y Membresías' : 'Reporte de Proyectos y Producción Digital'); ?>
    </h3>
    <span style="font-size: 0.84rem; color: #a5b4fc;">
      Período: <strong><?= htmlspecialchars(date('d/m/Y', strtotime($fechaInicio))); ?></strong> al <strong><?= htmlspecialchars(date('d/m/Y', strtotime($fechaFin))); ?></strong>
      <span class="badge ms-2" style="background: rgba(34, 197, 94, 0.2); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3);">
        <i class="bi bi-lightning-charge-fill me-1"></i> Actualizado en tiempo real
      </span>
    </span>
  </div>
  <div class="text-md-end text-start">
    <div style="font-size: 0.8rem; color: #94a3b8;">Generado por: <span class="text-white fw-semibold"><?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8'); ?></span></div>
    <div style="font-size: 0.75rem; color: #64748b;">Fecha de emisión: <?= date('d/m/Y H:i:s'); ?></div>
  </div>
</div>

<!-- ========================================== -->
<!-- CASO 1: REPORTE DE INGRESOS Y TRANSACCIONES -->
<!-- ========================================== -->
<?php if ($tipoReporte === 'ingresos'): ?>
  
  <!-- Tarjetas de Resumen -->
  <div class="stats-grid mb-4">
    <div class="stat-card-custom">
      <div class="stat-header">
        <span class="stat-header-label">TOTAL INGRESOS EN RANGO</span>
        <i class="bi bi-cash-coin stat-header-icon text-success"></i>
      </div>
      <div class="stat-number text-success">$<?= number_format((float)$totalRango, 2); ?></div>
      <span class="stat-pill stat-pill-green">sp_reporte_ingresos</span>
    </div>

    <div class="stat-card-custom">
      <div class="stat-header">
        <span class="stat-header-label">TRANSACCIONES DEL PERÍODO</span>
        <i class="bi bi-receipt stat-header-icon text-info"></i>
      </div>
      <div class="stat-number text-info"><?= count($transacciones); ?></div>
      <span class="stat-pill" style="background: rgba(14, 165, 233, 0.15); color: #7dd3fc; border: 1px solid rgba(14, 165, 233, 0.3);">En rango</span>
    </div>

    <div class="stat-card-custom">
      <div class="stat-header">
        <span class="stat-header-label">TICKET PROMEDIO</span>
        <i class="bi bi-calculator stat-header-icon text-warning"></i>
      </div>
      <div class="stat-number text-warning">$<?= number_format((float)$promedioRango, 2); ?></div>
      <span class="stat-pill" style="background: rgba(234, 179, 8, 0.15); color: #facc15; border: 1px solid rgba(234, 179, 8, 0.3);">USD por pago</span>
    </div>

    <div class="stat-card-custom">
      <div class="stat-header">
        <span class="stat-header-label">TOTAL HISTÓRICO GLOBAL</span>
        <i class="bi bi-vault stat-header-icon"></i>
      </div>
      <div class="stat-number">$<?= number_format((float)$totalIngresos, 2); ?></div>
      <span class="stat-pill stat-pill-gray">Todas las fechas</span>
    </div>
  </div>

  <!-- 1.1 Desglose Diario por Stored Procedure -->
  <div class="projects-box mb-4">
    <div class="projects-top d-flex justify-content-between align-items-center">
      <div>
        <h2 class="mb-1">Desglose Diario de Facturación</h2>
        <span style="font-size: 0.85rem; color: #a5b4fc;">
          Ejecutado con: <code>CALL sp_reporte_ingresos('<?= htmlspecialchars($fechaInicio); ?>', '<?= htmlspecialchars($fechaFin); ?>')</code>
        </span>
      </div>
    </div>

    <div class="table-responsive mt-3">
      <table class="table table-dark table-hover mb-0 align-middle" style="background: transparent;">
        <thead>
          <tr style="color: #94a3b8; font-size: 0.75rem; text-transform: uppercase; border-bottom: 1px solid rgba(255,255,255,0.1);">
            <th>Fecha</th>
            <th>Cantidad de Pagos</th>
            <th>Total Ingresos ($USD)</th>
            <th style="min-width: 140px;">Volumen Relativo</th>
          </tr>
        </thead>
        <tbody style="font-size: 0.88rem;">
          <?php if (!empty($reporteIngresos)): ?>
            <?php foreach ($reporteIngresos as $dia): ?>
              <?php
                $montoDia = (float)$dia['total_ingresos'];
                $porcentaje = ($maxIngresoDia > 0) ? round(($montoDia / $maxIngresoDia) * 100) : 0;
              ?>
              <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                <td class="fw-semibold text-white">
                  <i class="bi bi-calendar3 me-2 text-info"></i>
                  <?= htmlspecialchars(date('d/m/Y', strtotime($dia['fecha']))); ?>
                </td>
                <td>
                  <span class="badge" style="background: rgba(99, 102, 241, 0.2); color: #a5b4fc; border: 1px solid rgba(99, 102, 241, 0.3);">
                    <?= (int)$dia['num_pagos']; ?> pago(s)
                  </span>
                </td>
                <td class="fw-bold text-success" style="font-size: 0.95rem;">
                  $<?= number_format($montoDia, 2); ?> USD
                </td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="progress flex-grow-1" style="height: 8px; background: rgba(255,255,255,0.08); border-radius: 4px;">
                      <div class="progress-bar" role="progressbar" 
                           style="width: <?= $porcentaje; ?>%; background: linear-gradient(90deg, #6366f1 0%, #a855f7 100%); border-radius: 4px;"></div>
                    </div>
                    <span style="font-size: 0.75rem; color: #94a3b8; width: 35px;"><?= $porcentaje; ?>%</span>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="4" class="text-center text-muted py-4">No se registraron pagos completados en este rango de fechas.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- 1.2 Detalle de Cada Transacción en el Rango -->
  <div class="projects-box mb-4">
    <div class="projects-top d-flex justify-content-between align-items-center">
      <div>
        <h2 class="mb-1">Detalle de Transacciones Individuales (<?= count($transacciones); ?>)</h2>
        <span style="font-size: 0.85rem; color: #a5b4fc;">Registro línea por línea de compras y suscripciones</span>
      </div>
    </div>

    <div class="table-responsive mt-3">
      <table class="table table-dark table-hover mb-0 align-middle" style="background: transparent;">
        <thead>
          <tr style="color: #94a3b8; font-size: 0.75rem; text-transform: uppercase; border-bottom: 1px solid rgba(255,255,255,0.1);">
            <th>ID</th>
            <th>Fecha</th>
            <th>Cliente</th>
            <th>Plan</th>
            <th>Monto</th>
            <th>Método</th>
            <th>N° Transacción</th>
            <th>Estado</th>
          </tr>
        </thead>
        <tbody style="font-size: 0.88rem;">
          <?php if (!empty($transacciones)): ?>
            <?php foreach ($transacciones as $t): ?>
              <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                <td class="text-secondary fw-bold">#<?= (int)$t['id_pago']; ?></td>
                <td class="text-secondary" style="font-size: 0.82rem;">
                  <?= htmlspecialchars(date('d/m/Y H:i', strtotime($t['creado_en']))); ?>
                </td>
                <td>
                  <div class="fw-semibold text-white"><?= htmlspecialchars($t['nombre'] . ' ' . $t['apellido']); ?></div>
                  <div class="text-secondary" style="font-size: 0.75rem;"><?= htmlspecialchars($t['email']); ?></div>
                </td>
                <td>
                  <span class="badge" style="background: rgba(124, 58, 237, 0.2); color: #c4b5fd; border: 1px solid rgba(124, 58, 237, 0.4);">
                    <?= htmlspecialchars($t['nombre_plan'] ?? 'Sin Plan'); ?>
                  </span>
                </td>
                <td class="fw-bold text-success">
                  $<?= number_format((float)$t['monto'], 2); ?> <span style="font-size: 0.72rem; color: #94a3b8; font-weight: normal;">USD</span>
                </td>
                <td>
                  <span class="badge bg-secondary"><?= htmlspecialchars(ucfirst($t['metodo'])); ?></span>
                </td>
                <td class="text-secondary" style="font-family: monospace; font-size: 0.8rem;">
                  <?= !empty($t['nro_transaccion']) ? htmlspecialchars($t['nro_transaccion']) : '<span class="text-muted">N/D</span>'; ?>
                </td>
                <td>
                  <?php if ($t['estado'] === 'completado'): ?>
                    <span class="stat-pill stat-pill-green">Completado</span>
                  <?php else: ?>
                    <span class="stat-pill stat-pill-gray"><?= htmlspecialchars(ucfirst($t['estado'])); ?></span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" class="text-center text-muted py-4">No hay transacciones registradas en este período.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

<!-- ========================================== -->
<!-- CASO 2: REPORTE DE USUARIOS Y MEMBRESÍAS    -->
<!-- ========================================== -->
<?php elseif ($tipoReporte === 'usuarios'): ?>

  <div class="projects-box mb-4">
    <div class="projects-top d-flex justify-content-between align-items-center">
      <div>
        <h2 class="mb-1">Directorio Consolidado de Usuarios y Membresías</h2>
        <span style="font-size: 0.85rem; color: #a5b4fc;">Datos combinados de las vistas MySQL <code>v_usuarios</code> y <code>v_resumen_pagos</code></span>
      </div>
      <span class="badge" style="background: rgba(99, 102, 241, 0.2); color: #a5b4fc; font-size: 0.85rem; padding: 6px 12px;">
        Total: <?= count($usuariosReporte); ?> usuarios
      </span>
    </div>

    <div class="table-responsive mt-3">
      <table class="table table-dark table-hover mb-0 align-middle" style="background: transparent;">
        <thead>
          <tr style="color: #94a3b8; font-size: 0.75rem; text-transform: uppercase; border-bottom: 1px solid rgba(255,255,255,0.1);">
            <th>ID</th>
            <th>Usuario</th>
            <th>Correo</th>
            <th>Rol</th>
            <th>Estado</th>
            <th>Plan Activo</th>
            <th>Vencimiento</th>
            <th class="text-center">Pagos</th>
            <th class="text-end">Total Invertido</th>
            <th>Último Pago</th>
          </tr>
        </thead>
        <tbody style="font-size: 0.88rem;">
          <?php if (!empty($usuariosReporte)): ?>
            <?php foreach ($usuariosReporte as $u): ?>
              <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                <td class="text-secondary fw-semibold">#<?= (int)$u['id_usuario']; ?></td>
                <td class="fw-semibold text-white">
                  <?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido']); ?>
                </td>
                <td class="text-secondary"><?= htmlspecialchars($u['email']); ?></td>
                <td>
                  <span class="badge <?= strtolower($u['nomb_rol'] ?? '') === 'administrador' ? 'bg-primary' : 'bg-secondary'; ?>">
                    <?= htmlspecialchars(ucfirst($u['nomb_rol'] ?? 'cliente')); ?>
                  </span>
                </td>
                <td>
                  <?php if ($u['estado_usr'] === 'activo'): ?>
                    <span class="stat-pill stat-pill-green">Activo</span>
                  <?php elseif ($u['estado_usr'] === 'suspendido'): ?>
                    <span class="stat-pill" style="background: rgba(234, 179, 8, 0.15); color: #facc15;">Suspendido</span>
                  <?php elseif ($u['estado_usr'] === 'eliminado'): ?>
                    <span class="stat-pill" style="background: rgba(239, 68, 68, 0.15); color: #f87171;">Dado de baja</span>
                  <?php else: ?>
                    <span class="stat-pill stat-pill-gray"><?= htmlspecialchars(ucfirst($u['estado_usr'])); ?></span>
                  <?php endif; ?>
                </td>
                <td>
                  <span class="badge" style="background: rgba(14, 165, 233, 0.2); color: #7dd3fc;">
                    <?= htmlspecialchars($u['nombre_plan'] ?? 'Sin Plan'); ?>
                  </span>
                </td>
                <td class="text-secondary" style="font-size: 0.8rem;">
                  <?= !empty($u['suscripcion_vence']) ? htmlspecialchars($u['suscripcion_vence']) : '<span class="text-muted">N/A</span>'; ?>
                </td>
                <td class="text-center">
                  <span class="badge" style="background: rgba(124, 58, 237, 0.2); color: #c4b5fd;">
                    <?= (int)$u['total_pagos']; ?>
                  </span>
                </td>
                <td class="text-end fw-bold text-success">
                  $<?= number_format((float)$u['total_pagado'], 2); ?>
                </td>
                <td class="text-secondary" style="font-size: 0.8rem;">
                  <?= !empty($u['ultimo_pago']) ? htmlspecialchars(date('d/m/Y', strtotime($u['ultimo_pago']))) : '<span class="text-muted">Sin pagos</span>'; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="10" class="text-center text-muted py-4">No se encontraron usuarios.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

<!-- ========================================== -->
<!-- CASO 3: REPORTE DE PROYECTOS Y PATRONES    -->
<!-- ========================================== -->
<?php elseif ($tipoReporte === 'proyectos'): ?>

  <div class="projects-box mb-4">
    <div class="projects-top d-flex justify-content-between align-items-center">
      <div>
        <h2 class="mb-1">Reporte de Proyectos y Patrones Procesados</h2>
        <span style="font-size: 0.85rem; color: #a5b4fc;">Datos obtenidos de la tabla <code>patron</code> con autoría de <code>usuarios</code></span>
      </div>
      <span class="badge" style="background: rgba(99, 102, 241, 0.2); color: #a5b4fc; font-size: 0.85rem; padding: 6px 12px;">
        Total: <?= count($proyectosReporte); ?> proyectos
      </span>
    </div>

    <div class="table-responsive mt-3">
      <table class="table table-dark table-hover mb-0 align-middle" style="background: transparent;">
        <thead>
          <tr style="color: #94a3b8; font-size: 0.75rem; text-transform: uppercase; border-bottom: 1px solid rgba(255,255,255,0.1);">
            <th>ID</th>
            <th>Proyecto</th>
            <th>Tipo</th>
            <th>Autor / Cliente</th>
            <th>Correo</th>
            <th>Estado</th>
            <th>Fecha de Creación</th>
          </tr>
        </thead>
        <tbody style="font-size: 0.88rem;">
          <?php if (!empty($proyectosReporte)): ?>
            <?php foreach ($proyectosReporte as $p): ?>
              <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                <td class="text-secondary fw-semibold">#<?= (int)$p['id_patron']; ?></td>
                <td class="fw-semibold text-white">
                  <i class="bi bi-file-earmark-code me-2 text-info"></i>
                  <?= htmlspecialchars($p['nomb_patron']); ?>
                </td>
                <td>
                  <span class="badge" style="background: rgba(124, 58, 237, 0.2); color: #c4b5fd;">
                    <?= htmlspecialchars(ucfirst($p['tipo_patron'] ?? 'patronaje')); ?>
                  </span>
                </td>
                <td><?= htmlspecialchars($p['nombre'] . ' ' . $p['apellido']); ?></td>
                <td class="text-secondary"><?= htmlspecialchars($p['email']); ?></td>
                <td>
                  <?php if ($p['estado_patron'] === 'activo'): ?>
                    <span class="stat-pill stat-pill-green">Activo</span>
                  <?php else: ?>
                    <span class="stat-pill stat-pill-gray"><?= htmlspecialchars(ucfirst($p['estado_patron'])); ?></span>
                  <?php endif; ?>
                </td>
                <td class="text-secondary" style="font-size: 0.82rem;">
                  <?= htmlspecialchars(date('d/m/Y H:i', strtotime($p['fecha_creacion']))); ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="text-center text-muted py-4">No se encontraron proyectos en este rango.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

<?php endif; ?>
