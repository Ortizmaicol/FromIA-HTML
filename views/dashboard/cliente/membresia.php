<?php
$pageTitle         = "FormAI - Mi Membresía";
$extraCss          = "dashboard.css";
$nombre            = $nombre ?? ($_SESSION['user_nombre'] ?? 'Cliente');
$totalActivos      = $totalActivos ?? 0;
$suscripcion       = $suscripcion ?? null;
$historialPagos    = $historialPagos ?? [];
$planesDisponibles = $planesDisponibles ?? [];
$diasRestantes     = $diasRestantes ?? 0;
$mensajeExito      = $mensajeExito ?? null;
$mensajeError      = $mensajeError ?? null;

require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="dashboard-wrapper">
  <!-- Sidebar Cliente -->
  <aside class="sidebar-custom">
    <div>
      <div class="sidebar-group-title">PRINCIPAL</div>
      <ul class="sidebar-menu">
        <li><a href="/FromIA-HTML/dashboard.php"><i class="bi bi-house-door-fill"></i> Inicio</a></li>
        <li><a href="#"><i class="bi bi-download"></i> Descargar App <span class="sidebar-badge-red">NUEVO</span></a></li>
        <li><a href="#"><i class="bi bi-display"></i> Prueba Web Gratuita</a></li>
      </ul>
    </div>
    <div>
      <div class="sidebar-group-title">MI CUENTA</div>
      <ul class="sidebar-menu">
        <li><a href="/FromIA-HTML/dashboard.php?seccion=perfil"><i class="bi bi-person-fill"></i> Mi Perfil</a></li>
        <li><a href="/FromIA-HTML/dashboard.php?seccion=membresia" class="active"><i class="bi bi-gem"></i> Mi Membresía</a></li>
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

  <!-- Contenido Cliente: Gestión de Membresía -->
  <main class="main-panel">
    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
      <div>
        <h1 class="welcome-title mb-1">Mi Membresía y Facturación 💎</h1>
        <p class="welcome-subtitle mb-0">Adquiere un plan, simula el pago de tu membresía o renueva tu suscripción cuando venza.</p>
      </div>
    </div>

    <!-- Alertas de Feedback -->
    <?php if (!empty($mensajeExito)): ?>
      <div class="alert alert-success d-flex align-items-center mb-4" style="background: rgba(46, 204, 113, 0.15); border: 1px solid #2ecc71; color: #7bed9f; border-radius: 12px; padding: 14px 20px;" role="alert">
        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
        <div><?= htmlspecialchars($mensajeExito, ENT_QUOTES, 'UTF-8'); ?></div>
      </div>
    <?php endif; ?>

    <?php if (!empty($mensajeError)): ?>
      <div class="alert alert-danger d-flex align-items-center mb-4" style="background: rgba(231, 76, 60, 0.15); border: 1px solid #e74c3c; color: #ff8080; border-radius: 12px; padding: 14px 20px;" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
        <div><?= htmlspecialchars($mensajeError, ENT_QUOTES, 'UTF-8'); ?></div>
      </div>
    <?php endif; ?>

    <!-- 1. ESTADO DE LA MEMBRESÍA ACTUAL -->
    <div class="projects-box mb-4 p-4" style="background: linear-gradient(135deg, rgba(30, 20, 61, 0.85) 0%, rgba(15, 10, 33, 0.95) 100%); border: 1px solid rgba(124, 58, 237, 0.3); border-radius: 16px;">
      <?php if (empty($suscripcion)): ?>
        <!-- Caso A: Usuario Nuevo sin Membresía -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
          <div class="d-flex align-items-center gap-3">
            <div style="width: 56px; height: 56px; border-radius: 16px; background: rgba(124, 58, 237, 0.2); border: 1px solid rgba(124, 58, 237, 0.4); display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: #a78bfa;">
              <i class="bi bi-rocket-takeoff-fill"></i>
            </div>
            <div>
              <h3 class="text-white fw-bold mb-1 fs-5">¡Bienvenido a FormAI! Aún no tienes una membresía activa</h3>
              <p class="text-secondary mb-0" style="font-size: 0.88rem;">
                Elige uno de los siguientes planes para desbloquear el estudio de patronaje asistido por IA, exportaciones ilimitadas y soporte prioritario.
              </p>
            </div>
          </div>
          <span class="stat-pill stat-pill-gray py-2 px-3">Sin Membresía</span>
        </div>

      <?php elseif (($suscripcion['estado_real'] ?? '') === 'vencida'): ?>
        <!-- Caso B: Membresía Vencida -->
        <div class="p-3 rounded-3 mb-3" style="background: rgba(239, 68, 68, 0.12); border: 1px solid rgba(239, 68, 68, 0.35);">
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
              <div style="width: 50px; height: 50px; border-radius: 12px; background: rgba(239, 68, 68, 0.2); display: flex; align-items: center; justify-content: center; font-size: 1.6rem; color: #f87171;">
                <i class="bi bi-exclamation-octagon-fill"></i>
              </div>
              <div>
                <h4 class="text-white fw-bold mb-1 fs-5">Tu membresía <?= htmlspecialchars($suscripcion['nombre_plan']); ?> ha vencido</h4>
                <div style="font-size: 0.85rem; color: #fca5a5;">
                  El período contratado expiró el <strong><?= date('d/m/Y', strtotime($suscripcion['fecha_fin'])); ?></strong> (hace <?= (int)($suscripcion['dias_vencido'] ?? 0); ?> días). Puedes renovarlo de inmediato o cambiar a otro plan.
                </div>
              </div>
            </div>

            <button type="button" class="btn btn-success" 
                    onclick="abrirModalPago(<?= (int)$suscripcion['id_plan']; ?>, '<?= htmlspecialchars(addslashes($suscripcion['nombre_plan'])); ?>', <?= (float)$suscripcion['precio_mensual']; ?>, <?= (float)$suscripcion['precio_anual']; ?>)"
                    style="border-radius: 20px; font-weight: 700; padding: 10px 22px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none;">
              <i class="bi bi-arrow-repeat me-1"></i> Renovar Plan <?= htmlspecialchars($suscripcion['nombre_plan']); ?>
            </button>
          </div>
        </div>

      <?php else: ?>
        <!-- Caso C: Membresía Activa -->
        <div class="row align-items-center g-4">
          <div class="col-12 col-md-5" style="border-right: 1px solid rgba(255, 255, 255, 0.08);">
            <div class="d-flex align-items-center gap-3 mb-2">
              <div style="width: 52px; height: 52px; border-radius: 14px; background: rgba(34, 197, 94, 0.15); border: 1px solid rgba(34, 197, 94, 0.3); display: flex; align-items: center; justify-content: center; font-size: 1.6rem; color: #4ade80;">
                <i class="bi bi-gem"></i>
              </div>
              <div>
                <span style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; font-weight: 700;">Tu Plan Activo</span>
                <h3 class="text-white mb-0 fw-bold fs-4"><?= htmlspecialchars($suscripcion['nombre_plan'], ENT_QUOTES, 'UTF-8'); ?></h3>
              </div>
            </div>
            <div class="mt-2">
              <span class="stat-pill stat-pill-green py-1 px-3">
                <i class="bi bi-check-circle-fill me-1"></i> Activa (<?= ucfirst(htmlspecialchars($suscripcion['ciclo'])); ?>)
              </span>
            </div>
          </div>

          <div class="col-12 col-md-7">
            <div class="row g-3 text-start">
              <div class="col-6 col-sm-4">
                <div style="font-size: 0.72rem; color: #8a87a2; font-weight: 700;">PRECIO ACTUAL</div>
                <div class="text-white fw-bold fs-5 mt-1">
                  $<?= number_format(($suscripcion['ciclo'] === 'anual' ? $suscripcion['precio_anual'] : $suscripcion['precio_mensual']), 2); ?>
                  <span style="font-size: 0.7rem; color: #888;">USD</span>
                </div>
              </div>

              <div class="col-6 col-sm-4">
                <div style="font-size: 0.72rem; color: #8a87a2; font-weight: 700;">PRÓXIMO VENCIMIENTO</div>
                <div class="text-white fw-bold mt-1" style="font-size: 0.95rem;">
                  <?= date('d/m/Y', strtotime($suscripcion['fecha_fin'])); ?>
                </div>
              </div>

              <div class="col-12 col-sm-4">
                <div style="font-size: 0.72rem; color: #8a87a2; font-weight: 700;">DÍAS RESTANTES</div>
                <div class="text-white fw-bold mt-1 fs-5" style="color: #4ade80 !important;">
                  <?= (int)$diasRestantes; ?> <span style="font-size: 0.8rem; font-weight: normal; color: #ccc;">días</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <!-- 2. CATÁLOGO DE PLANES Y PRECIOS -->
    <div class="text-center mt-5 mb-4">
      <h2 class="text-white fw-bold mb-2">Planes y Membresías Disponibles</h2>
      <p class="text-secondary mb-3">Escoge tu plan y simula la compra al instante para activar todas las funcionalidades.</p>

      <!-- Toggle Ciclo Mensual / Anual -->
      <div class="d-inline-flex align-items-center gap-3 p-2 rounded-pill" style="background: #150f2b; border: 1px solid rgba(255,255,255,0.1);">
        <span id="lblToggleMensual" style="color: #ffffff; font-weight: 600; font-size: 0.88rem; cursor: pointer;" onclick="cambiarCiclo('mensual')">Mensual</span>
        <div class="form-check form-switch m-0" style="padding-left: 2.5em;">
          <input class="form-check-input" type="checkbox" role="switch" id="toggleCiclo" onchange="toggleCicloSwitch(this)" style="cursor: pointer; width: 40px; height: 20px;">
        </div>
        <span id="lblToggleAnual" style="color: #888888; font-weight: 600; font-size: 0.88rem; cursor: pointer;" onclick="cambiarCiclo('anual')">
          Anual <span class="badge bg-success ms-1" style="font-size: 0.7rem;">Ahorra 20%</span>
        </span>
      </div>
    </div>

    <!-- Grid de Tarjetas de Planes -->
    <div class="row g-4 justify-content-center mb-5">
      <?php foreach ($planesDisponibles as $p): ?>
        <?php
          $idPlan = (int)$p['id_plan'];
          $esPlanActual = (!empty($suscripcion) && (int)$suscripcion['id_plan'] === $idPlan);
          $esVencida = (!empty($suscripcion) && ($suscripcion['estado_real'] ?? '') === 'vencida');
          $esPopular = ($idPlan === 2); // Profesional como Popular
        ?>
        <div class="col-12 col-md-6 col-lg-4">
          <div class="h-100 p-4 rounded-4 position-relative d-flex flex-column justify-content-between" 
               style="background: #0f0a21; border: <?= $esPopular ? '2px solid #7c3aed' : '1px solid rgba(255,255,255,0.1)'; ?>; box-shadow: <?= $esPopular ? '0 8px 30px rgba(124, 58, 237, 0.25)' : 'none'; ?>;">
            
            <?php if ($esPopular): ?>
              <span class="badge position-absolute top-0 start-50 translate-middle" 
                    style="background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%); padding: 6px 16px; border-radius: 20px; font-weight: 700; text-transform: uppercase; font-size: 0.75rem;">
                ⭐ Más Popular
              </span>
            <?php endif; ?>

            <div>
              <div class="d-flex align-items-center justify-content-between mb-3">
                <h3 class="text-white fw-bold mb-0 fs-4">
                  <?php if ($idPlan === 1): ?><i class="bi bi-mortarboard me-2 text-info"></i>
                  <?php elseif ($idPlan === 2): ?><i class="bi bi-lightning-charge me-2 text-primary"></i>
                  <?php else: ?><i class="bi bi-building me-2 text-warning"></i><?php endif; ?>
                  <?= htmlspecialchars($p['nombre_plan']); ?>
                </h3>
                <?php if ($esPlanActual && !$esVencida): ?>
                  <span class="badge bg-success" style="font-size: 0.72rem;">Actual</span>
                <?php endif; ?>
              </div>

              <!-- Precio Dinámico según Ciclo -->
              <div class="mb-3">
                <div class="price-mensual">
                  <span class="fs-1 fw-extrabold text-white">$<?= number_format((float)$p['precio_mensual'], 2); ?></span>
                  <span class="text-secondary" style="font-size: 0.85rem;">/ mes</span>
                </div>
                <div class="price-anual d-none">
                  <span class="fs-1 fw-extrabold text-white">$<?= number_format((float)$p['precio_anual'], 2); ?></span>
                  <span class="text-secondary" style="font-size: 0.85rem;">/ año facturado</span>
                </div>
              </div>

              <p class="text-secondary mb-4" style="font-size: 0.88rem; min-height: 42px;">
                <?php if ($idPlan === 1): ?>
                  Ideal para estudiantes y creadores independientes que inician con patronaje asistido.
                <?php elseif ($idPlan === 2): ?>
                  Diseñado para talleres y marcas de moda que requieren exportaciones ilimitadas y 3D.
                <?php else: ?>
                  Solución corporativa para fábricas textiles con soporte dedicado y múltiples accesos.
                <?php endif; ?>
              </p>

              <hr style="border-color: rgba(255,255,255,0.08);">

              <!-- Características -->
              <div class="mb-4">
                <span style="font-size: 0.75rem; color: #a5b4fc; text-transform: uppercase; font-weight: 700;">Incluye:</span>
                <ul class="list-unstyled mt-2 text-secondary" style="font-size: 0.85rem; line-height: 1.9;">
                  <?php if ($idPlan === 1): ?>
                    <li><i class="bi bi-check2 text-success me-2"></i> Hasta 15 proyectos activos</li>
                    <li><i class="bi bi-check2 text-success me-2"></i> Exportación en formato JSON FormAI</li>
                    <li><i class="bi bi-check2 text-success me-2"></i> Asistente de IA básico</li>
                    <li><i class="bi bi-check2 text-success me-2"></i> Soporte por correo estándar</li>
                  <?php elseif ($idPlan === 2): ?>
                    <li><i class="bi bi-check2 text-success me-2"></i> Proyectos ilimitados</li>
                    <li><i class="bi bi-check2 text-success me-2"></i> Exportaciones DXF, PDF, SVG y JSON</li>
                    <li><i class="bi bi-check2 text-success me-2"></i> Simulación y moldería 3D</li>
                    <li><i class="bi bi-check2 text-success me-2"></i> Gradación automática de tallas</li>
                    <li><i class="bi bi-check2 text-success me-2"></i> Soporte prioritario 24/7</li>
                  <?php else: ?>
                    <li><i class="bi bi-check2 text-success me-2"></i> Todo lo incluido en Profesional</li>
                    <li><i class="bi bi-check2 text-success me-2"></i> Integración API corporativa</li>
                    <li><i class="bi bi-check2 text-success me-2"></i> Múltiples cuentas colaborativas</li>
                    <li><i class="bi bi-check2 text-success me-2"></i> Consultoría y capacitación VIP</li>
                  <?php endif; ?>
                </ul>
              </div>
            </div>

            <!-- Botón de Acción -->
            <div>
              <?php if ($esPlanActual && !$esVencida): ?>
                <button type="button" class="btn btn-outline-secondary w-100 py-2 disabled" style="border-radius: 10px; border-color: rgba(255,255,255,0.2);">
                  <i class="bi bi-check-circle me-1"></i> Plan Actual Activo
                </button>
              <?php elseif ($esPlanActual && $esVencida): ?>
                <button type="button" class="btn btn-success w-100 py-2 fw-bold" 
                        onclick="abrirModalPago(<?= $idPlan; ?>, '<?= htmlspecialchars(addslashes($p['nombre_plan'])); ?>', <?= (float)$p['precio_mensual']; ?>, <?= (float)$p['precio_anual']; ?>)"
                        style="border-radius: 10px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none;">
                  <i class="bi bi-arrow-repeat me-1"></i> Renovar este Plan
                </button>
              <?php else: ?>
                <button type="button" class="btn w-100 py-2 fw-bold <?= $esPopular ? 'btn-primary' : 'btn-outline-light'; ?>" 
                        onclick="abrirModalPago(<?= $idPlan; ?>, '<?= htmlspecialchars(addslashes($p['nombre_plan'])); ?>', <?= (float)$p['precio_mensual']; ?>, <?= (float)$p['precio_anual']; ?>)"
                        style="border-radius: 10px; <?= $esPopular ? 'background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%); border: none;' : 'border-color: rgba(255,255,255,0.25);'; ?>">
                  <?= empty($suscripcion) ? 'Elegir este Plan' : 'Cambiar a este Plan'; ?>
                </button>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- 3. HISTORIAL DE FACTURAS Y TRANSACCIONES -->
    <div class="projects-box">
      <div class="projects-top d-flex justify-content-between align-items-center">
        <div>
          <h2 class="mb-1">Historial de Compras y Facturas</h2>
          <span style="font-size: 0.85rem; color: #a5b4fc;">Comprobantes de pago registrados en tu cuenta</span>
        </div>
      </div>

      <div class="table-responsive mt-3">
        <table class="table table-dark table-hover mb-0 align-middle" style="background: transparent;">
          <thead>
            <tr style="color: #94a3b8; font-size: 0.75rem; text-transform: uppercase; border-bottom: 1px solid rgba(255,255,255,0.1);">
              <th>#ID</th>
              <th>Plan Adquirido</th>
              <th>Monto</th>
              <th>Método</th>
              <th>N° de Transacción</th>
              <th>Fecha</th>
              <th>Estado</th>
            </tr>
          </thead>
          <tbody style="font-size: 0.88rem;">
            <?php if (!empty($historialPagos)): ?>
              <?php foreach ($historialPagos as $p): ?>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                  <td class="text-secondary fw-bold">#<?= (int)$p['id_pago']; ?></td>
                  <td>
                    <span class="badge" style="background: rgba(124, 58, 237, 0.2); color: #c4b5fd; border: 1px solid rgba(124, 58, 237, 0.4);">
                      <?= htmlspecialchars($p['nombre_plan'] ?? 'Membresía FormAI'); ?>
                    </span>
                  </td>
                  <td class="fw-bold text-success">
                    $<?= number_format((float)$p['monto'], 2); ?> <span style="font-size: 0.75rem; color: #94a3b8; font-weight: normal;"><?= htmlspecialchars($p['moneda'] ?? 'USD'); ?></span>
                  </td>
                  <td>
                    <span class="badge bg-secondary"><?= htmlspecialchars(ucfirst($p['metodo'])); ?></span>
                  </td>
                  <td class="text-secondary" style="font-family: monospace; font-size: 0.82rem;">
                    <?= !empty($p['nro_transaccion']) ? htmlspecialchars($p['nro_transaccion']) : '<span class="text-muted">N/D</span>'; ?>
                  </td>
                  <td class="text-secondary" style="font-size: 0.82rem;">
                    <?= htmlspecialchars(date('d/m/Y H:i', strtotime($p['creado_en']))); ?>
                  </td>
                  <td>
                    <?php if ($p['estado'] === 'completado'): ?>
                      <span class="stat-pill stat-pill-green">Completado</span>
                    <?php else: ?>
                      <span class="stat-pill stat-pill-gray"><?= htmlspecialchars(ucfirst($p['estado'])); ?></span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" class="text-center text-muted py-4">
                  Aún no tienes comprobantes de pago registrados.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>

<!-- MODAL DE EMULACIÓN DE VENTA / PAGO -->
<div class="modal fade" id="modalEmulacionPago" tabindex="-1" aria-labelledby="modalEmulacionPagoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="background: #140d2e; border: 1px solid rgba(124, 58, 237, 0.4); border-radius: 18px; color: #ffffff;">
      <div class="modal-header" style="border-bottom: 1px solid rgba(255,255,255,0.08);">
        <h5 class="modal-title fw-bold" id="modalEmulacionPagoLabel">
          <i class="bi bi-credit-card-2-front-fill me-2 text-primary"></i>
          Pasarela de Pago Simulada &bull; FormAI
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form method="POST" action="/FromIA-HTML/dashboard.php?seccion=membresia">
        <input type="hidden" name="accion" value="emular_pago_membresia">
        <input type="hidden" id="modal_input_plan" name="id_plan" value="">
        <input type="hidden" id="modal_input_ciclo" name="ciclo" value="mensual">

        <div class="modal-body p-4">
          <!-- Resumen de Compra -->
          <div class="p-3 rounded-3 mb-4" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-secondary" style="font-size: 0.85rem;">Membresía elegida:</span>
              <span id="modal_resumen_plan" class="fw-bold text-white fs-6">Plan Profesional</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-secondary" style="font-size: 0.85rem;">Periodo de facturación:</span>
              <span id="modal_resumen_ciclo" class="badge bg-primary">Mensual</span>
            </div>
            <hr style="border-color: rgba(255,255,255,0.1); margin: 8px 0;">
            <div class="d-flex justify-content-between align-items-center">
              <span class="fw-bold text-white">Total a pagar:</span>
              <span id="modal_resumen_total" class="fw-extrabold text-success fs-4">$24.99 USD</span>
            </div>
          </div>

          <!-- Selección del Método de Pago -->
          <div class="mb-3">
            <label class="form-label" style="font-size: 0.8rem; color: #a5b4fc; text-transform: uppercase; font-weight: 700;">
              Método de Pago
            </label>
            <div class="d-flex gap-2 mb-3">
              <input type="radio" class="btn-check" name="metodo" id="metodo_tarjeta" value="tarjeta" checked onchange="cambiarMetodoModal('tarjeta')">
              <label class="btn btn-outline-light flex-grow-1" for="metodo_tarjeta" style="font-size: 0.82rem; border-color: rgba(255,255,255,0.2);">
                <i class="bi bi-credit-card me-1"></i> Tarjeta
              </label>

              <input type="radio" class="btn-check" name="metodo" id="metodo_paypal" value="paypal" onchange="cambiarMetodoModal('paypal')">
              <label class="btn btn-outline-light flex-grow-1" for="metodo_paypal" style="font-size: 0.82rem; border-color: rgba(255,255,255,0.2);">
                <i class="bi bi-paypal me-1"></i> PayPal
              </label>

              <input type="radio" class="btn-check" name="metodo" id="metodo_transferencia" value="transferencia" onchange="cambiarMetodoModal('transferencia')">
              <label class="btn btn-outline-light flex-grow-1" for="metodo_transferencia" style="font-size: 0.82rem; border-color: rgba(255,255,255,0.2);">
                <i class="bi bi-bank me-1"></i> Transferencia
              </label>
            </div>
          </div>

          <!-- Campos simulados de Tarjeta -->
          <div id="campos_tarjeta">
            <div class="mb-3">
              <label class="form-label" style="font-size: 0.75rem; color: #94a3b8;">Número de Tarjeta (Simulado)</label>
              <div class="input-group">
                <span class="input-group-text" style="background: #1f1642; border-color: rgba(255,255,255,0.15); color: #ccc;">
                  <i class="bi bi-credit-card-2-front"></i>
                </span>
                <input type="text" class="form-control" value="4532 8921 4452 7819" 
                       style="background: #1f1642; border-color: rgba(255,255,255,0.15); color: #fff;">
              </div>
            </div>

            <div class="row g-2 mb-3">
              <div class="col-7">
                <label class="form-label" style="font-size: 0.75rem; color: #94a3b8;">Expiración</label>
                <input type="text" class="form-control" value="12/28" 
                       style="background: #1f1642; border-color: rgba(255,255,255,0.15); color: #fff;">
              </div>
              <div class="col-5">
                <label class="form-label" style="font-size: 0.75rem; color: #94a3b8;">CVC / CVV</label>
                <input type="password" class="form-control" value="842" 
                       style="background: #1f1642; border-color: rgba(255,255,255,0.15); color: #fff;">
              </div>
            </div>
          </div>

          <!-- Mensaje simulado de PayPal -->
          <div id="campos_paypal" class="d-none p-3 rounded-3 mb-3 text-center" style="background: rgba(14, 165, 233, 0.1); border: 1px solid rgba(14, 165, 233, 0.3);">
            <i class="bi bi-paypal text-info fs-3 d-block mb-1"></i>
            <span style="font-size: 0.85rem; color: #bae6fd;">Se autorizará el cargo instantáneo a tu cuenta PayPal vinculada.</span>
          </div>

          <!-- Mensaje simulado de Transferencia -->
          <div id="campos_transferencia" class="d-none p-3 rounded-3 mb-3 text-center" style="background: rgba(234, 179, 8, 0.1); border: 1px solid rgba(234, 179, 8, 0.3);">
            <i class="bi bi-bank text-warning fs-3 d-block mb-1"></i>
            <span style="font-size: 0.85rem; color: #fef08a;">Se generará una referencia de transferencia bancaria inmediata.</span>
          </div>

          <p class="text-muted text-center mb-0" style="font-size: 0.75rem;">
            <i class="bi bi-shield-lock-fill text-success me-1"></i> Transacción simulada segura en entorno de desarrollo.
          </p>
        </div>

        <div class="modal-footer" style="border-top: 1px solid rgba(255,255,255,0.08);">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success px-4 fw-bold" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none;">
            <i class="bi bi-check-circle-fill me-1"></i> Confirmar y Emular Pago
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
let cicloSeleccionado = 'mensual';

function toggleCicloSwitch(checkbox) {
  cambiarCiclo(checkbox.checked ? 'anual' : 'mensual');
}

function cambiarCiclo(ciclo) {
  cicloSeleccionado = ciclo;
  const toggle = document.getElementById('toggleCiclo');
  const lblM = document.getElementById('lblToggleMensual');
  const lblA = document.getElementById('lblToggleAnual');

  if (toggle) toggle.checked = (ciclo === 'anual');

  if (ciclo === 'anual') {
    lblA.style.color = '#ffffff';
    lblM.style.color = '#888888';
    document.querySelectorAll('.price-mensual').forEach(el => el.classList.add('d-none'));
    document.querySelectorAll('.price-anual').forEach(el => el.classList.remove('d-none'));
  } else {
    lblM.style.color = '#ffffff';
    lblA.style.color = '#888888';
    document.querySelectorAll('.price-anual').forEach(el => el.classList.add('d-none'));
    document.querySelectorAll('.price-mensual').forEach(el => el.classList.remove('d-none'));
  }
}

function abrirModalPago(idPlan, nombrePlan, precioMensual, precioAnual) {
  const inputPlan  = document.getElementById('modal_input_plan');
  const inputCiclo = document.getElementById('modal_input_ciclo');
  const resPlan    = document.getElementById('modal_resumen_plan');
  const resCiclo   = document.getElementById('modal_resumen_ciclo');
  const resTotal   = document.getElementById('modal_resumen_total');

  const monto = (cicloSeleccionado === 'anual') ? precioAnual : precioMensual;

  if (inputPlan)  inputPlan.value = idPlan;
  if (inputCiclo) inputCiclo.value = cicloSeleccionado;
  if (resPlan)    resPlan.textContent = 'Plan ' + nombrePlan;
  if (resCiclo)   resCiclo.textContent = cicloSeleccionado === 'anual' ? 'Anual (12 Meses)' : 'Mensual (30 Días)';
  if (resTotal)   resTotal.textContent = '$' + monto.toFixed(2) + ' USD';

  const modalEl = document.getElementById('modalEmulacionPago');
  if (modalEl && typeof bootstrap !== 'undefined') {
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
  }
}

function cambiarMetodoModal(metodo) {
  const secTarjeta = document.getElementById('campos_tarjeta');
  const secPaypal  = document.getElementById('campos_paypal');
  const secTransf  = document.getElementById('campos_transferencia');

  if (secTarjeta) secTarjeta.classList.toggle('d-none', metodo !== 'tarjeta');
  if (secPaypal)  secPaypal.classList.toggle('d-none', metodo !== 'paypal');
  if (secTransf)  secTransf.classList.toggle('d-none', metodo !== 'transferencia');
}
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
