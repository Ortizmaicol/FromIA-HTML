<?php
$pageTitle      = "FormAI - Mi Perfil";
$extraCss       = "dashboard.css";
$nombre         = $nombre ?? ($_SESSION['user_nombre'] ?? 'Cliente');
$totalActivos   = $totalActivos ?? (isset($proyectos) && is_array($proyectos) ? count($proyectos) : 0);
$suscripcion    = $suscripcion ?? null;
$historialPagos = $historialPagos ?? [];
$diasRestantes  = $diasRestantes ?? 0;
$usuarioActual  = $usuarioActual ?? [];
$mensajeExito   = $mensajeExito ?? null;
$mensajeError   = $mensajeError ?? null;

require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="dashboard-wrapper">
  <!-- Sidebar Cliente -->
  <aside class="sidebar-custom">
    <div>
      <div class="sidebar-group-title">PRINCIPAL</div>
      <ul class="sidebar-menu">
        <li><a href="/FromIA-HTML/dashboard.php"><i class="bi bi-house-door-fill"></i> Inicio</a></li>
        <li><a href="/FromIA-HTML/dashboard.php?seccion=workspace"><i class="bi bi-vector-pen"></i> Aplicación <span class="sidebar-badge-red">NUEVO</span></a></li>
        <li><a href="/FromIA-HTML/dashboard.php?seccion=workspace"><i class="bi bi-display"></i> Prueba Web Gratuita</a></li>
      </ul>
    </div>
    <div>
      <div class="sidebar-group-title">MI CUENTA</div>
      <ul class="sidebar-menu">
        <li><a href="/FromIA-HTML/dashboard.php?seccion=perfil" class="active"><i class="bi bi-person-fill"></i> Mi Perfil</a></li>
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

  <!-- Contenido Cliente: Mi Perfil -->
  <main class="main-panel">
    
    <div class="mb-4">
      <h1 class="welcome-title mb-1">Mi Perfil 👤</h1>
      <p class="welcome-subtitle mb-0">Gestiona tu información personal, datos de contacto, membresía e historial de facturación.</p>
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

    <!-- SECCIÓN 1: DATOS DE USUARIO Y EDICIÓN DE CONTACTO -->
    <div class="stat-card-custom mb-4" style="background: #0f0a21; border: 1px solid rgba(255, 255, 255, 0.08);">
      <div class="d-flex align-items-center justify-content-between mb-4 pb-3" style="border-bottom: 1px solid rgba(255, 255, 255, 0.06);">
        <div class="d-flex align-items-center gap-3">
          <div style="width: 52px; height: 52px; border-radius: 50%; background: linear-gradient(135deg, #6c5ce7 0%, #336699 100%); display: flex; align-items: center; justify-content: center; font-size: 1.6rem; color: #ffffff;">
            <i class="bi bi-person-fill"></i>
          </div>
          <div>
            <h2 class="text-white mb-0" style="font-size: 1.3rem; font-weight: 700;">
              <?= htmlspecialchars(($usuarioActual['nombre'] ?? 'Usuario') . ' ' . ($usuarioActual['apellido'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
            </h2>
            <span style="font-size: 0.8rem; color: #8c89a4;">
              Cuenta de usuario registrada
            </span>
          </div>
        </div>

        <div>
          <span class="stat-pill stat-pill-green">
            <i class="bi bi-shield-check me-1"></i> <?= ucfirst(htmlspecialchars($usuarioActual['estado_usr'] ?? 'activo')); ?>
          </span>
        </div>
      </div>

      <form method="POST" action="/FromIA-HTML/dashboard.php?seccion=perfil" novalidate>
        <input type="hidden" name="accion" value="actualizar_perfil">

        <div class="row g-3">
          <!-- Campo 1: Nombre (Solo lectura) -->
          <div class="col-12 col-md-6">
            <label class="form-label" style="font-size: 0.78rem; font-weight: 700; color: #8a87a2; text-transform: uppercase;">
              Nombre
            </label>
            <div class="input-group">
              <span class="input-group-text" style="background-color: #0b071a; border-color: rgba(255,255,255,0.1); color: #888;">
                <i class="bi bi-lock-fill"></i>
              </span>
              <input type="text" class="form-control" value="<?= htmlspecialchars($usuarioActual['nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" style="background-color: #0b071a; border-color: rgba(255,255,255,0.1); color: #aaa; cursor: not-allowed;" readonly disabled />
            </div>
          </div>

          <!-- Campo 2: Apellido (Solo lectura) -->
          <div class="col-12 col-md-6">
            <label class="form-label" style="font-size: 0.78rem; font-weight: 700; color: #8a87a2; text-transform: uppercase;">
              Apellido
            </label>
            <div class="input-group">
              <span class="input-group-text" style="background-color: #0b071a; border-color: rgba(255,255,255,0.1); color: #888;">
                <i class="bi bi-lock-fill"></i>
              </span>
              <input type="text" class="form-control" value="<?= htmlspecialchars($usuarioActual['apellido'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" style="background-color: #0b071a; border-color: rgba(255,255,255,0.1); color: #aaa; cursor: not-allowed;" readonly disabled />
            </div>
          </div>

          <!-- Campo 3: Rol (Solo lectura) -->
          <div class="col-12 col-md-6">
            <label class="form-label" style="font-size: 0.78rem; font-weight: 700; color: #8a87a2; text-transform: uppercase;">
              Rol Asignado
            </label>
            <div class="input-group">
              <span class="input-group-text" style="background-color: #0b071a; border-color: rgba(255,255,255,0.1); color: #888;">
                <i class="bi bi-person-badge"></i>
              </span>
              <input type="text" class="form-control" value="<?= htmlspecialchars(ucfirst($usuarioActual['rol'] ?? 'Cliente'), ENT_QUOTES, 'UTF-8'); ?>" style="background-color: #0b071a; border-color: rgba(255,255,255,0.1); color: #aaa; cursor: not-allowed;" readonly disabled />
            </div>
          </div>

          <!-- Campo 4: Fecha Registro (Solo lectura) -->
          <div class="col-12 col-md-6">
            <label class="form-label" style="font-size: 0.78rem; font-weight: 700; color: #8a87a2; text-transform: uppercase;">
              Fecha de Registro
            </label>
            <div class="input-group">
              <span class="input-group-text" style="background-color: #0b071a; border-color: rgba(255,255,255,0.1); color: #888;">
                <i class="bi bi-calendar-event"></i>
              </span>
              <input type="text" class="form-control" value="<?= !empty($usuarioActual['creado_en']) ? date('d/m/Y H:i', strtotime($usuarioActual['creado_en'])) : 'N/A'; ?>" style="background-color: #0b071a; border-color: rgba(255,255,255,0.1); color: #aaa; cursor: not-allowed;" readonly disabled />
            </div>
          </div>

          <!-- Campo 5: Correo Electrónico (EDITABLE) -->
          <div class="col-12 col-md-6 mt-4">
            <label for="inputEmail" class="form-label" style="font-size: 0.78rem; font-weight: 700; color: #5dade2; text-transform: uppercase;">
              <i class="bi bi-pencil-square me-1"></i> Correo Electrónico (Editable)
            </label>
            <div class="input-group">
              <span class="input-group-text" style="background-color: #17112e; border-color: rgba(93, 173, 226, 0.4); color: #5dade2;">
                <i class="bi bi-envelope-fill"></i>
              </span>
              <input type="email" id="inputEmail" name="email" class="form-control" value="<?= htmlspecialchars($usuarioActual['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required style="background-color: #17112e; border-color: rgba(93, 173, 226, 0.4); color: #ffffff;" />
            </div>
            <div class="form-text" style="color: #8c89a4; font-size: 0.75rem;">Se utilizará para iniciar sesión y recibir notificaciones.</div>
          </div>

          <!-- Campo 6: Teléfono (EDITABLE) -->
          <div class="col-12 col-md-6 mt-4">
            <label for="inputTelefono" class="form-label" style="font-size: 0.78rem; font-weight: 700; color: #5dade2; text-transform: uppercase;">
              <i class="bi bi-pencil-square me-1"></i> Teléfono (Editable)
            </label>
            <div class="input-group">
              <span class="input-group-text" style="background-color: #17112e; border-color: rgba(93, 173, 226, 0.4); color: #5dade2;">
                <i class="bi bi-telephone-fill"></i>
              </span>
              <input type="tel" id="inputTelefono" name="telefono" class="form-control" value="<?= htmlspecialchars($usuarioActual['telefono'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Ej: 3001234567" style="background-color: #17112e; border-color: rgba(93, 173, 226, 0.4); color: #ffffff;" />
            </div>
            <div class="form-text" style="color: #8c89a4; font-size: 0.75rem;">Número de contacto directo (7 a 15 dígitos).</div>
          </div>
        </div>

        <div class="d-flex flex-wrap justify-content-between align-items-center mt-4 pt-3" style="border-top: 1px solid rgba(255, 255, 255, 0.06);">
          <small style="color: #8c89a4; font-size: 0.78rem;">
            <i class="bi bi-info-circle me-1"></i> Solo el correo y teléfono pueden ser modificados por el usuario.
          </small>
          <button type="submit" class="btn btn-primary" style="border-radius: 20px; font-weight: 600; padding: 9px 24px;">
            <i class="bi bi-check2-circle me-1"></i> Guardar Cambios
          </button>
        </div>
      </form>
    </div>

    <!-- SECCIÓN 2: ESTADO DE LA MEMBRESÍA -->
    <div class="stat-card-custom mb-4" style="background: linear-gradient(135deg, rgba(23, 16, 51, 0.9) 0%, rgba(11, 7, 26, 0.95) 100%); border: 1px solid rgba(255, 255, 255, 0.1);">
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <h3 class="text-white mb-0" style="font-size: 1.15rem; font-weight: 700;">
          <i class="bi bi-gem me-2 text-info"></i> Estado de tu Membresía
        </h3>
        <div>
          <?php if (!empty($suscripcion) && ($suscripcion['estado_real'] ?? '') === 'vencida'): ?>
            <a href="/FromIA-HTML/dashboard.php?seccion=membresia" class="btn btn-success btn-sm" style="border-radius: 20px; font-weight: 600; padding: 6px 18px; background-color: #27ae60; border: none;">
              <i class="bi bi-arrow-repeat me-1"></i> Renovar Membresía
            </a>
          <?php else: ?>
            <a href="/FromIA-HTML/dashboard.php?seccion=membresia" class="btn btn-primary btn-sm" style="border-radius: 20px; font-weight: 600; padding: 6px 18px;">
              <i class="bi bi-arrow-repeat me-1"></i> Cambiar / Mejorar Plan
            </a>
          <?php endif; ?>
        </div>
      </div>

      <div class="row align-items-center g-4 pt-2">
        <div class="col-12 col-md-5" style="border-right: 1px solid rgba(255, 255, 255, 0.08);">
          <div class="d-flex align-items-center gap-3 mb-2">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(51, 102, 153, 0.25); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; color: #5dade2;">
              <i class="bi bi-gem"></i>
            </div>
            <div>
              <span style="font-size: 0.72rem; color: #8a87a2; text-transform: uppercase; font-weight: 700;">Plan Actual</span>
              <h4 class="text-white mb-0" style="font-size: 1.4rem; font-weight: 800;">
                <?= !empty($suscripcion) ? htmlspecialchars($suscripcion['nombre_plan'], ENT_QUOTES, 'UTF-8') : 'Plan Gratuito'; ?>
              </h4>
            </div>
          </div>

          <div class="mt-3">
            <?php if (!empty($suscripcion)): ?>
              <?php if (($suscripcion['estado_real'] ?? '') === 'activa'): ?>
                <span class="stat-pill stat-pill-green" style="font-size: 0.78rem; padding: 4px 12px;">
                  <i class="bi bi-check-circle-fill me-1"></i> Estado: Activa
                </span>
              <?php elseif (($suscripcion['estado_real'] ?? '') === 'vencida'): ?>
                <span class="stat-pill" style="font-size: 0.78rem; padding: 4px 12px; background: rgba(231, 76, 60, 0.2); color: #ff6b6b; border: 1px solid rgba(231, 76, 60, 0.4);">
                  <i class="bi bi-exclamation-triangle-fill me-1"></i> Estado: Vencida
                </span>
              <?php else: ?>
                <span class="stat-pill stat-pill-gray" style="font-size: 0.78rem; padding: 4px 12px;">
                  <i class="bi bi-x-circle-fill me-1"></i> Estado: <?= ucfirst(htmlspecialchars($suscripcion['estado_real'] ?? $suscripcion['estado_scrip'])); ?>
                </span>
              <?php endif; ?>
              <span class="stat-pill stat-pill-gray ms-2" style="font-size: 0.78rem; padding: 4px 12px;">
                Ciclo: <?= ucfirst(htmlspecialchars($suscripcion['ciclo'])); ?>
              </span>
            <?php else: ?>
              <span class="stat-pill stat-pill-gray" style="font-size: 0.78rem; padding: 4px 12px;">
                Sin suscripción activa
              </span>
            <?php endif; ?>
          </div>
        </div>

        <div class="col-12 col-md-7">
          <div class="row g-3 text-start">
            <div class="col-6 col-sm-3">
              <div style="font-size: 0.72rem; color: #8a87a2; font-weight: 700;">PRECIO</div>
              <div class="text-white fw-bold fs-5 mt-1">
                <?php if (!empty($suscripcion)): ?>
                  $<?= number_format(($suscripcion['ciclo'] === 'anual' ? $suscripcion['precio_anual'] : $suscripcion['precio_mensual']), 2); ?>
                  <span style="font-size: 0.7rem; color: #888;">USD</span>
                <?php else: ?>
                  $0.00
                <?php endif; ?>
              </div>
            </div>

            <div class="col-6 col-sm-3">
              <div style="font-size: 0.72rem; color: #8a87a2; font-weight: 700;">INICIO</div>
              <div class="text-white fw-bold mt-1" style="font-size: 0.92rem;">
                <?= !empty($suscripcion['fecha_inicio']) ? date('d/m/Y', strtotime($suscripcion['fecha_inicio'])) : 'N/A'; ?>
              </div>
            </div>

            <div class="col-6 col-sm-3">
              <div style="font-size: 0.72rem; color: #8a87a2; font-weight: 700;">VENCE / FACTURA</div>
              <div class="text-white fw-bold mt-1" style="font-size: 0.92rem;">
                <?= !empty($suscripcion['fecha_fin']) ? date('d/m/Y', strtotime($suscripcion['fecha_fin'])) : 'N/A'; ?>
                <?php if (!empty($suscripcion) && ($suscripcion['estado_real'] ?? '') === 'vencida'): ?>
                  <div style="font-size: 0.7rem; color: #ff6b6b; font-weight: 600;">(Periodo Expirado)</div>
                <?php endif; ?>
              </div>
            </div>

            <div class="col-6 col-sm-3">
              <div style="font-size: 0.72rem; color: #8a87a2; font-weight: 700;">DÍAS RESTANTES</div>
              <div class="text-white fw-bold mt-1" style="font-size: 0.92rem;">
                <?php if (!empty($suscripcion) && ($suscripcion['estado_real'] ?? '') === 'activa'): ?>
                  <span style="color: <?= $diasRestantes > 5 ? '#2ecc71' : '#f39c12'; ?>; font-weight: 800; font-size: 1.05rem;">
                    <?= (int)$diasRestantes; ?>
                  </span> días
                <?php elseif (!empty($suscripcion) && ($suscripcion['estado_real'] ?? '') === 'vencida'): ?>
                  <span style="color: #ff6b6b; font-weight: 800; font-size: 1rem;">
                    0 días
                  </span>
                  <div style="font-size: 0.7rem; color: #ff8080; font-weight: 500;">
                    Venció hace <?= (int)($suscripcion['dias_vencido'] ?? 0); ?> d
                  </div>
                <?php else: ?>
                  <span style="color: #888;">0 días</span>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- SECCIÓN 3: HISTORIAL DE PAGOS Y FACTURAS -->
    <div class="projects-box">
      <div class="projects-top">
        <h2>Historial de Facturas y Pagos</h2>
        <span style="font-size: 0.85rem; color: #888;">Registro contable de tu cuenta</span>
      </div>

      <div class="table-responsive">
        <table class="table table-dark table-hover mb-0 align-middle" style="background: transparent;">
          <thead>
            <tr style="background: rgba(30, 20, 60, 0.8); border-bottom: 2px solid rgba(255, 255, 255, 0.12);">
              <th style="padding: 16px 20px; font-size: 0.88rem; font-weight: 800; color: #c8c4e6; letter-spacing: 0.6px; text-transform: uppercase;">N° FACTURA</th>
              <th style="padding: 16px 16px; font-size: 0.88rem; font-weight: 800; color: #c8c4e6; letter-spacing: 0.6px; text-transform: uppercase;">CONCEPTO / PLAN</th>
              <th style="padding: 16px 16px; font-size: 0.88rem; font-weight: 800; color: #c8c4e6; letter-spacing: 0.6px; text-transform: uppercase;">MONTO</th>
              <th style="padding: 16px 16px; font-size: 0.88rem; font-weight: 800; color: #c8c4e6; letter-spacing: 0.6px; text-transform: uppercase;">MÉTODO</th>
              <th style="padding: 16px 16px; font-size: 0.88rem; font-weight: 800; color: #c8c4e6; letter-spacing: 0.6px; text-transform: uppercase;">FECHA</th>
              <th style="padding: 16px 20px; font-size: 0.88rem; font-weight: 800; color: #c8c4e6; letter-spacing: 0.6px; text-transform: uppercase; text-align: right;">ESTADO</th>
            </tr>
          </thead>
          <tbody style="font-family: 'Montserrat', sans-serif; font-size: 0.88rem; color: #ffffff;">
            <?php if (!empty($historialPagos)): ?>
              <?php foreach ($historialPagos as $pago): ?>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.06); color: #ffffff;">
                  <td style="padding: 16px 20px; color: #ffffff; font-weight: 500;">
                    <?= htmlspecialchars(!empty($pago['nro_transaccion']) ? $pago['nro_transaccion'] : ('FAC-' . str_pad($pago['id_pago'], 6, '0', STR_PAD_LEFT))); ?>
                  </td>
                  <td style="padding: 16px 16px; color: #ffffff; font-weight: 500;">
                    <?= htmlspecialchars($pago['nombre_plan'] ?? 'Membresía FormAI'); ?>
                  </td>
                  <td style="padding: 16px 16px; color: #ffffff; font-weight: 500;">
                    $<?= number_format((float)$pago['monto'], 2); ?> <?= htmlspecialchars($pago['moneda'] ?? 'USD'); ?>
                  </td>
                  <td style="padding: 16px 16px; color: #ffffff; font-weight: 500; text-transform: capitalize;">
                    <?= htmlspecialchars($pago['metodo']); ?>
                  </td>
                  <td style="padding: 16px 16px; color: #ffffff; font-weight: 500;">
                    <?= date('d/m/Y H:i', strtotime($pago['creado_en'])); ?>
                  </td>
                  <td style="padding: 16px 20px; text-align: right;">
                    <?php if ($pago['estado'] === 'completado'): ?>
                      <span class="stat-pill stat-pill-green">Completado</span>
                    <?php elseif ($pago['estado'] === 'pendiente'): ?>
                      <span class="stat-pill" style="color: #f39c12; background: rgba(243, 156, 18, 0.15);">Pendiente</span>
                    <?php else: ?>
                      <span class="stat-pill stat-pill-gray"><?= htmlspecialchars(ucfirst($pago['estado'])); ?></span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" class="text-center text-muted py-4">
                  <i class="bi bi-receipt fs-3 d-block mb-2 text-secondary"></i>
                  No tienes transacciones registradas en este momento.
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
