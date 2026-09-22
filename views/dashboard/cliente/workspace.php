<?php
$pageTitle = "FormAI - Aplicación de Patronaje";
$extraCss  = "dashboard.css";
require_once __DIR__ . '/../../layouts/header.php';

$patronCargado = $patronCargado ?? null;
$nombrePatronInicial = $patronCargado ? htmlspecialchars($patronCargado['nomb_patron'], ENT_QUOTES, 'UTF-8') : 'Patrón sin título';
$idPatronInicial = $patronCargado ? (int)$patronCargado['id_patron'] : 0;
$tipoPatronInicial = $patronCargado ? htmlspecialchars($patronCargado['tipo_patron'], ENT_QUOTES, 'UTF-8') : 'patronaje';
$descPatronInicial = $patronCargado ? htmlspecialchars($patronCargado['desc_patron'] ?? '', ENT_QUOTES, 'UTF-8') : '';
$imagenInicial = ($patronCargado && !empty($patronCargado['ubicacion'])) ? '/FromIA-HTML/' . htmlspecialchars($patronCargado['ubicacion'], ENT_QUOTES, 'UTF-8') : '';
?>

<style>
  /* RESET Y ESTRUCTURA GENERAL DE LA APLICACIÓN */
  body {
    overflow: hidden !important;
  }

  .workspace-layout {
    display: grid;
    grid-template-columns: 68px 1fr;
    width: 100vw;
    height: calc(100vh - 65px);
    background-color: #050508;
    overflow: hidden;
    font-family: 'Montserrat', sans-serif;
  }

  /* BARRA LATERAL DE HERRAMIENTAS (68px) */
  .workspace-sidebar {
    grid-column: 1 / 2;
    background: #090615;
    border-right: 1px solid rgba(255, 255, 255, 0.08);
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 14px 0;
    gap: 6px;
    user-select: none;
    z-index: 25;
  }

  .sidebar-divider {
    width: 36px;
    height: 1px;
    background: rgba(255, 255, 255, 0.1);
    margin: 6px 0;
  }

  .tool-btn {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    border: 1px solid transparent;
    background: transparent;
    color: #94a3b8;
    font-size: 1.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
    user-select: none;
  }

  .tool-btn:hover {
    background: rgba(124, 58, 237, 0.2);
    color: #ffffff;
    border-color: rgba(124, 58, 237, 0.4);
    transform: translateY(-1px);
  }

  .tool-btn.active {
    background: linear-gradient(135deg, rgba(124, 58, 237, 0.4) 0%, rgba(99, 102, 241, 0.4) 100%) !important;
    color: #ffffff !important;
    border-color: #7c3aed !important;
    box-shadow: 0 0 14px rgba(124, 58, 237, 0.5) !important;
  }

  /* Tooltip personalizado */
  .tool-btn::after {
    content: attr(data-tooltip);
    position: absolute;
    left: calc(100% + 10px);
    top: 50%;
    transform: translateY(-50%);
    background: #1e153a;
    color: #ffffff;
    font-size: 0.72rem;
    font-weight: 600;
    padding: 5px 11px;
    border-radius: 6px;
    white-space: nowrap;
    pointer-events: none;
    opacity: 0;
    transition: opacity 0.15s ease;
    border: 1px solid rgba(255, 255, 255, 0.15);
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.4);
    z-index: 100;
  }

  .tool-btn:hover::after {
    opacity: 1;
  }

  /* ÁREA DEL CANVAS */
  .workspace-canvas-area {
    grid-column: 2 / 3;
    display: flex;
    flex-direction: column;
    background-color: #07040f;
    position: relative;
    overflow: hidden;
  }

  /* BARRA SUPERIOR DE CONTROL Y EDICIÓN */
  .canvas-topbar {
    min-height: 48px;
    background: #0c081d;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 16px;
    gap: 12px;
    flex-wrap: nowrap;
    flex-shrink: 0;
    z-index: 20;
    user-select: none;
  }

  .canvas-topbar-left,
  .canvas-topbar-center,
  .canvas-topbar-right {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .canvas-topbar-title {
    font-size: 0.85rem;
    font-weight: 700;
    color: #ffffff;
    letter-spacing: 0.3px;
    max-width: 220px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .canvas-topbar-badge {
    font-size: 0.68rem;
    font-weight: 700;
    background: rgba(124, 58, 237, 0.25);
    color: #c4b5fd;
    border: 1px solid rgba(124, 58, 237, 0.5);
    padding: 2px 8px;
    border-radius: 6px;
    text-transform: uppercase;
  }

  /* Selector de colores */
  .color-dot {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    cursor: pointer;
    border: 2px solid transparent;
    transition: all 0.2s ease;
  }

  .color-dot:hover {
    transform: scale(1.18);
  }

  .color-dot.active {
    border-color: #ffffff;
    box-shadow: 0 0 10px currentColor;
    transform: scale(1.1);
  }

  /* Botones de acción superior */
  .btn-topbar {
    padding: 6px 13px;
    font-size: 0.78rem;
    font-weight: 600;
    border-radius: 8px;
    border: 1px solid rgba(255, 255, 255, 0.12);
    background: rgba(255, 255, 255, 0.05);
    color: #e2e8f0;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
    cursor: pointer;
    text-decoration: none;
    user-select: none;
  }

  .btn-topbar:hover {
    background: rgba(255, 255, 255, 0.14);
    color: #ffffff;
    border-color: rgba(255, 255, 255, 0.3);
  }

  .btn-topbar-primary {
    background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);
    border-color: #7c3aed;
    color: #ffffff;
    font-weight: 700;
    box-shadow: 0 2px 10px rgba(124, 58, 237, 0.35);
  }

  .btn-topbar-primary:hover {
    background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);
    box-shadow: 0 4px 14px rgba(124, 58, 237, 0.55);
    color: #ffffff;
  }

  /* WRAPPER DEL LIENZO CON CUADRÍCULA TEXTIL */
  .canvas-wrapper {
    flex: 1;
    position: relative;
    overflow: hidden;
    background-color: #0b0817;
    background-image:
      linear-gradient(rgba(255, 255, 255, 0.035) 1px, transparent 1px),
      linear-gradient(90deg, rgba(255, 255, 255, 0.035) 1px, transparent 1px);
    background-size: 40px 40px;
  }

  #lienzoPatronaje {
    display: block;
    position: absolute;
    top: 0;
    left: 0;
    cursor: crosshair;
    touch-action: none;
  }

  #lienzoPatronaje.eraser-cursor {
    cursor: cell;
  }

  /* BARRA DE ESTADO INFERIOR */
  .canvas-statusbar {
    height: 30px;
    background: #090615;
    border-top: 1px solid rgba(255, 255, 255, 0.06);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 16px;
    flex-shrink: 0;
    font-size: 0.72rem;
    color: #8a87a2;
    user-select: none;
    z-index: 15;
  }

  .statusbar-group {
    display: flex;
    align-items: center;
    gap: 20px;
  }

  .statusbar-item {
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .statusbar-item i {
    color: #7c3aed;
  }

  /* Modal oscuro para guardar */
  .modal-workspace {
    background: #0f0a21;
    border: 1px solid rgba(124, 58, 237, 0.35);
    border-radius: 16px;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.6);
  }
</style>

<main class="workspace-layout">

  <!-- ==================== SIDEBAR DE HERRAMIENTAS ==================== -->
  <aside class="workspace-sidebar" role="toolbar" aria-label="Herramientas de patronaje">

    <!-- 1. Pluma -->
    <button type="button" class="tool-btn active" data-tool="pen" data-tooltip="Pluma (P)" title="Dibujo libre">
      <i class="bi bi-pencil-fill"></i>
    </button>

    <!-- 2. Línea recta con medida en cm y snap 45° -->
    <button type="button" class="tool-btn" data-tool="line" data-tooltip="Línea recta (L - Shift para 45°)" title="Línea recta con medición">
      <i class="bi bi-slash-lg"></i>
    </button>

    <!-- 3. Rectángulo -->
    <button type="button" class="tool-btn" data-tool="rect" data-tooltip="Rectángulo (R)" title="Dibujar rectángulo">
      <i class="bi bi-square"></i>
    </button>

    <!-- 4. Elipse / Círculo -->
    <button type="button" class="tool-btn" data-tool="ellipse" data-tooltip="Elipse / Círculo (O)" title="Dibujar elipse o círculo">
      <i class="bi bi-circle"></i>
    </button>

    <!-- 5. Curva Bézier -->
    <button type="button" class="tool-btn" data-tool="curve" data-tooltip="Curva Bézier (C)" title="Curva Bézier: Clic inicio, arrastra a fin, mueve control, confirma">
      <i class="bi bi-bezier"></i>
    </button>

    <!-- 6. Herramienta Texto -->
    <button type="button" class="tool-btn" data-tool="text" data-tooltip="Texto / Rotulación (T)" title="Texto: Clic en el lienzo y escribe">
      <i class="bi bi-cursor-text"></i>
    </button>

    <div class="sidebar-divider" role="separator"></div>

    <!-- 7. Selección -->
    <button type="button" class="tool-btn" data-tool="select" data-tooltip="Seleccionar (S)" title="Seleccionar y mover">
      <i class="bi bi-cursor-fill"></i>
    </button>

    <!-- 8. Borrador -->
    <button type="button" class="tool-btn" data-tool="eraser" data-tooltip="Borrador (E)" title="Borrador de precisión">
      <i class="bi bi-eraser-fill"></i>
    </button>

    <div class="sidebar-divider" role="separator"></div>

    <!-- 9. Limpiar Lienzo -->
    <button type="button" class="tool-btn" data-tool="clear" data-tooltip="Limpiar lienzo" title="Limpiar todo el lienzo">
      <i class="bi bi-trash3"></i>
    </button>

  </aside>
  <!-- FIN SIDEBAR -->


  <!-- ==================== ÁREA PRINCIPAL CANVAS ==================== -->
  <section class="workspace-canvas-area" aria-label="Lienzo de dibujo de patrones">

    <!-- BARRA SUPERIOR -->
    <div class="canvas-topbar">
      <!-- Izquierda: Volver, Acciones de Proyecto y Título -->
      <div class="canvas-topbar-left">
        <a href="/FromIA-HTML/dashboard.php?seccion=proyectos" class="btn-topbar" title="Volver al listado de proyectos">
          <i class="bi bi-arrow-left"></i>
          <span class="d-none d-lg-inline">Proyectos</span>
        </a>

        <!-- Botón Iniciar Nuevo Proyecto -->
        <button type="button" class="btn-topbar" id="btnNuevoProyecto" title="Crear un lienzo en blanco para un nuevo proyecto">
          <i class="bi bi-file-earmark-plus text-success"></i>
          <span>Nuevo</span>
        </button>

        <!-- Botón Elegir Proyecto Existente -->
        <button type="button" class="btn-topbar" id="btnAbrirModalProyectos" title="Elegir y cargar un proyecto existente">
          <i class="bi bi-folder2-open text-warning"></i>
          <span>Abrir</span>
        </button>

        <span class="canvas-topbar-title" id="lblNombrePatron" title="<?= $nombrePatronInicial; ?>">
          <?= $nombrePatronInicial; ?>
        </span>

        <span class="canvas-topbar-badge" id="lblBadgeEstado">
          <?= $patronCargado ? 'Proyecto #' . $idPatronInicial : 'Nuevo Proyecto'; ?>
        </span>
      </div>

      <!-- Centro: Paleta de Colores y Grosor -->
      <div class="canvas-topbar-center d-none d-md-flex">
        <!-- Selector de colores directos -->
        <div class="d-flex align-items-center gap-2 px-2 py-1 rounded-pill" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08);">
          <div class="color-dot active" data-color="#ffffff" style="background: #ffffff;" title="Blanco"></div>
          <div class="color-dot" data-color="#00f2fe" style="background: #00f2fe;" title="Cian FormAI"></div>
          <div class="color-dot" data-color="#a855f7" style="background: #a855f7;" title="Púrpura FormAI"></div>
          <div class="color-dot" data-color="#10b981" style="background: #10b981;" title="Verde Esmeralda"></div>
          <div class="color-dot" data-color="#f59e0b" style="background: #f59e0b;" title="Ámbar"></div>
          <div class="color-dot" data-color="#ef4444" style="background: #ef4444;" title="Rojo Coral"></div>
          <input type="color" id="pickerColorPersonalizado" value="#ffffff" style="width: 22px; height: 22px; border: none; padding: 0; background: transparent; cursor: pointer; border-radius: 50%;" title="Color personalizado">
        </div>

        <!-- Selector de Grosor -->
        <select id="selectGrosor" class="form-select form-select-sm" style="background: #150f2b; color: #ffffff; border: 1px solid rgba(255,255,255,0.1); width: 135px; font-size: 0.75rem; cursor: pointer;">
          <option value="1">1px (Fino)</option>
          <option value="2" selected>2px (Normal)</option>
          <option value="4">4px (Grueso)</option>
          <option value="8">8px (Marcador)</option>
        </select>
      </div>

      <!-- Derecha: Deshacer / Rehacer / Exportar / Guardar -->
      <div class="canvas-topbar-right">
        <!-- Deshacer -->
        <button type="button" class="btn-topbar" id="btnDeshacer" title="Deshacer (Ctrl+Z)">
          <i class="bi bi-arrow-counterclockwise"></i>
        </button>
        <!-- Rehacer -->
        <button type="button" class="btn-topbar" id="btnRehacer" title="Rehacer (Ctrl+Y)">
          <i class="bi bi-arrow-clockwise"></i>
        </button>

        <!-- Exportar PNG -->
        <button type="button" class="btn-topbar" id="btnExportarPng" title="Descargar imagen PNG del trazo">
          <i class="bi bi-file-image"></i>
          <span class="d-none d-sm-inline">Exportar</span>
        </button>

        <!-- Guardar Proyecto -->
        <button type="button" class="btn-topbar btn-topbar-primary" id="btnAbrirModalGuardar">
          <i class="bi bi-floppy-fill"></i>
          <span>Guardar</span>
        </button>
      </div>
    </div>

    <!-- WRAPPER CON CANVAS INTERACTIVO -->
    <div class="canvas-wrapper" id="canvasWrapper">
      <canvas id="lienzoPatronaje" aria-label="Lienzo de dibujo y patronaje textil"></canvas>
    </div>

    <!-- BARRA DE ESTADO INFERIOR -->
    <div class="canvas-statusbar">
      <div class="statusbar-group">
        <span class="statusbar-item" id="statusCoords">
          <i class="bi bi-crosshair"></i>
          <span id="coordX">0</span>, <span id="coordY">0</span> px &nbsp;(<span id="coordCm">0.0, 0.0</span> cm)
        </span>
        <span class="statusbar-item" id="statusTool">
          <i class="bi bi-tools"></i>
          Herramienta: <strong id="activeTool" class="text-light ms-1">Pluma</strong>
        </span>
      </div>

      <div class="statusbar-group d-none d-md-flex">
        <span class="statusbar-item" style="color: #635f79;">
          <i class="bi bi-info-circle"></i>
          Atajos: [Shift] Snap 45° &nbsp;|&nbsp; [Ctrl+Z] Deshacer &nbsp;|&nbsp; [Enter] Fijar Texto
        </span>
      </div>
    </div>

  </section>

</main>


<!-- ============================================================
     MODAL PARA GUARDAR PROYECTO EN LA BASE DE DATOS
============================================================ -->
<div class="modal fade" id="modalGuardarPatron" tabindex="-1" aria-labelledby="modalGuardarPatronLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modal-workspace">
      
      <div class="modal-header border-bottom" style="border-color: rgba(255,255,255,0.08) !important;">
        <h5 class="modal-title text-white fw-bold d-flex align-items-center gap-2" id="modalGuardarPatronLabel">
          <i class="bi bi-cloud-arrow-up-fill text-primary"></i>
          <span><?= $patronCargado ? 'Guardar Cambios del Proyecto' : 'Guardar Nuevo Proyecto'; ?></span>
        </h5>
        <button type="button" class="btn-close btn-close-white btn-cerrar-modal" aria-label="Cerrar"></button>
      </div>

      <form id="formGuardarPatron" method="POST">
        <input type="hidden" name="accion" value="guardar_patron_workspace">
        <input type="hidden" name="es_ajax" value="1">
        <input type="hidden" name="id_patron" id="guardarIdPatron" value="<?= $idPatronInicial; ?>">
        <input type="hidden" name="imagen_base64" id="guardarImagenBase64">
        <input type="hidden" name="archivo_json" id="guardarArchivoJson">

        <div class="modal-body p-4">
          <div class="mb-3">
            <label for="guardarNombre" class="form-label text-light fw-semibold" style="font-size: 0.85rem;">Nombre del Proyecto *</label>
            <input type="text" id="guardarNombre" name="nomb_patron" class="form-control" 
                   value="<?= $nombrePatronInicial !== 'Patrón sin título' ? $nombrePatronInicial : 'Mi Trazo FormAI'; ?>" 
                   style="background: #16102a; border: 1px solid rgba(255,255,255,0.12); color: #fff;" required maxlength="120">
          </div>

          <div class="mb-3">
            <label for="guardarTipo" class="form-label text-light fw-semibold" style="font-size: 0.85rem;">Tipo de Proyecto *</label>
            <select id="guardarTipo" name="tipo_patron" class="form-select" style="background: #16102a; border: 1px solid rgba(255,255,255,0.12); color: #fff;" required>
              <option value="patronaje" <?= $tipoPatronInicial === 'patronaje' ? 'selected' : ''; ?>>Patronaje (Molde / Trazo)</option>
              <option value="diseño" <?= $tipoPatronInicial === 'diseño' ? 'selected' : ''; ?>>Diseño Textil</option>
              <option value="3d" <?= $tipoPatronInicial === '3d' ? 'selected' : ''; ?>>Simulación 3D</option>
              <option value="mixto" <?= $tipoPatronInicial === 'mixto' ? 'selected' : ''; ?>>Mixto</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="guardarDesc" class="form-label text-light fw-semibold" style="font-size: 0.85rem;">Descripción o Notas</label>
            <textarea id="guardarDesc" name="desc_patron" class="form-control" rows="3" 
                      placeholder="Medidas, holguras, detalles de confección..."
                      style="background: #16102a; border: 1px solid rgba(255,255,255,0.12); color: #fff;"><?= $descPatronInicial; ?></textarea>
          </div>

          <?php if ($patronCargado): ?>
            <div class="form-check mb-2">
              <input class="form-check-input" type="checkbox" id="checkGuardarComoNuevo" name="guardar_como_nuevo" value="1">
              <label class="form-check-label text-light" for="checkGuardarComoNuevo" style="font-size: 0.85rem;">
                Guardar como un proyecto nuevo independiente (hacer copia)
              </label>
            </div>
          <?php endif; ?>
        </div>

        <div class="modal-footer border-top" style="border-color: rgba(255,255,255,0.08) !important;">
          <button type="button" class="btn btn-secondary btn-cerrar-modal" style="border-radius: 8px;">Cancelar</button>
          <button type="submit" class="btn btn-primary fw-bold" id="btnSubmitGuardar" style="border-radius: 8px; background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%); border: none;">
            <i class="bi bi-check-circle-fill me-1"></i> Confirmar y Guardar
          </button>
        </div>
      </form>

    </div>
  </div>
</div>


<!-- ============================================================
     MODAL PARA ELEGIR PROYECTO EXISTENTE O INICIAR UNO NUEVO
============================================================ -->
<div class="modal fade" id="modalElegirProyecto" tabindex="-1" aria-labelledby="modalElegirProyectoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content modal-workspace">
      
      <div class="modal-header border-bottom" style="border-color: rgba(255,255,255,0.08) !important;">
        <div class="d-flex align-items-center gap-2">
          <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(124, 58, 237, 0.2); display: flex; align-items: center; justify-content: center; color: #a855f7; font-size: 1.2rem;">
            <i class="bi bi-folder2-open"></i>
          </div>
          <div>
            <h5 class="modal-title text-white fw-bold mb-0" id="modalElegirProyectoLabel">Mis Proyectos de Patronaje</h5>
            <div style="font-size: 0.78rem; color: #8a87a2;">Selecciona un proyecto para abrirlo en el lienzo o inicia uno nuevo</div>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white btn-cerrar-modal-proyectos" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body p-3 p-md-4">
        <!-- Barra de acción en el modal: Buscador + Botón Nuevo en Blanco -->
        <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
          <div class="input-group" style="max-width: 380px;">
            <span class="input-group-text" style="background: #16102a; border-color: rgba(255,255,255,0.12); color: #8a87a2;">
              <i class="bi bi-search"></i>
            </span>
            <input type="text" id="filtroProyectosModal" class="form-control" placeholder="Buscar por nombre o tipo..." style="background: #16102a; border-color: rgba(255,255,255,0.12); color: #fff; font-size: 0.85rem;">
          </div>

          <button type="button" class="btn btn-sm btn-outline-success fw-semibold d-flex align-items-center gap-1" id="btnNuevoDesdeModal" style="border-radius: 8px; padding: 7px 16px;">
            <i class="bi bi-plus-circle-fill"></i> Iniciar Proyecto en Blanco
          </button>
        </div>

        <!-- Lista de proyectos con scroll -->
        <div class="proyectos-modal-scroll" style="max-height: 400px; overflow-y: auto; padding-right: 4px;">
          <?php if (!empty($proyectos)): ?>
            <div class="row g-2" id="gridProyectosModal">
              <?php foreach ($proyectos as $pr): ?>
                <?php
                  $idPr = (int)$pr['id_patron'];
                  $esActual = ($idPatronInicial === $idPr);
                  $tieneImg = (!empty($pr['ubicacion']) && file_exists(__DIR__ . '/../../' . $pr['ubicacion']));
                  $tipoPr = strtolower($pr['tipo_patron'] ?? 'patronaje');
                  $badgeColor = $tipoPr === 'diseño' ? '#38bdf8' : ($tipoPr === '3d' ? '#fbbf24' : '#a855f7');
                ?>
                <div class="col-12 col-md-6 item-proyecto-modal" 
                     data-nombre="<?= htmlspecialchars(strtolower($pr['nomb_patron']), ENT_QUOTES, 'UTF-8'); ?>"
                     data-tipo="<?= htmlspecialchars(strtolower($pr['tipo_patron'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                  <div class="p-3 rounded-3 d-flex align-items-center justify-content-between h-100" 
                       style="background: <?= $esActual ? 'rgba(124, 58, 237, 0.15)' : '#130c25'; ?>; border: 1px solid <?= $esActual ? '#7c3aed' : 'rgba(255,255,255,0.08)'; ?>; transition: all 0.2s ease;">
                    
                    <div class="d-flex align-items-center gap-3 overflow-hidden">
                      <div style="width: 46px; height: 46px; min-width: 46px; border-radius: 8px; background: #1c1438; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: #a5b4fc; overflow: hidden; border: 1px solid rgba(255,255,255,0.1);">
                        <?php if ($tieneImg): ?>
                          <img src="/FromIA-HTML/<?= htmlspecialchars($pr['ubicacion']); ?>" alt="Preview" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                          <i class="bi <?= $tipoPr === '3d' ? 'bi-box' : ($tipoPr === 'diseño' ? 'bi-palette' : 'bi-vector-pen'); ?>"></i>
                        <?php endif; ?>
                      </div>

                      <div class="overflow-hidden">
                        <div class="d-flex align-items-center gap-2">
                          <h6 class="text-white fw-bold mb-0 text-truncate" style="font-size: 0.88rem;" title="<?= htmlspecialchars($pr['nomb_patron']); ?>">
                            <?= htmlspecialchars($pr['nomb_patron']); ?>
                          </h6>
                          <?php if ($esActual): ?>
                            <span class="badge bg-success" style="font-size: 0.65rem;">Abierto</span>
                          <?php endif; ?>
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-1">
                          <span class="badge" style="background: rgba(255,255,255,0.06); color: <?= $badgeColor; ?>; font-size: 0.68rem; text-transform: uppercase;">
                            <?= htmlspecialchars($pr['tipo_patron'] ?? 'Patronaje'); ?>
                          </span>
                          <span style="font-size: 0.72rem; color: #716c87;">
                            <?= !empty($pr['fecha_creacion']) ? date('d/m/Y', strtotime($pr['fecha_creacion'])) : ''; ?>
                          </span>
                        </div>
                      </div>
                    </div>

                    <div>
                      <?php if ($esActual): ?>
                        <button type="button" class="btn btn-sm btn-outline-light disabled" style="font-size: 0.75rem; border-radius: 6px;" disabled>
                          En uso
                        </button>
                      <?php else: ?>
                        <button type="button" class="btn btn-sm btn-primary fw-semibold btn-cargar-proyecto" 
                                data-id="<?= $idPr; ?>"
                                data-nombre="<?= htmlspecialchars($pr['nomb_patron'], ENT_QUOTES, 'UTF-8'); ?>"
                                style="font-size: 0.75rem; border-radius: 6px; background: #7c3aed; border-color: #7c3aed;">
                          <i class="bi bi-box-arrow-in-right me-1"></i> Abrir
                        </button>
                      <?php endif; ?>
                    </div>

                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <div class="text-center py-4 text-muted">
              <i class="bi bi-folder2-open fs-1 d-block mb-2 text-secondary"></i>
              Aún no tienes otros proyectos guardados en tu cuenta.
            </div>
          <?php endif; ?>
          <div id="noResultadosFiltro" class="text-center py-4 text-muted d-none">
            <i class="bi bi-search fs-2 d-block mb-2"></i>
            No se encontraron proyectos con ese término.
          </div>
        </div>
      </div>

      <div class="modal-footer border-top" style="border-color: rgba(255,255,255,0.08) !important;">
        <button type="button" class="btn btn-secondary btn-cerrar-modal-proyectos" style="border-radius: 8px;">Cerrar</button>
      </div>

    </div>
  </div>
</div>


<!-- Carga explícita de librerías para garantizar disponibilidad inmediata -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- ============================================================
     MOTOR DE DIBUJO JAVASCRIPT DEL WORKSPACE (VANILLA JS + CANVAS 2D)
============================================================ -->
<script>
(function() {
  console.log("🎨 Aplicación de Patronaje FormAI inicializando...");

  // 1. REFERENCIAS DEL DOM
  const canvas                = document.getElementById('lienzoPatronaje');
  const ctx                   = canvas.getContext('2d', { willReadFrequently: true });
  const wrapper               = document.getElementById('canvasWrapper');
  const toolButtons           = document.querySelectorAll('.tool-btn[data-tool]');
  const spanCoordX            = document.getElementById('coordX');
  const spanCoordY            = document.getElementById('coordY');
  const spanCoordCm           = document.getElementById('coordCm');
  const spanTool              = document.getElementById('activeTool');
  const colorDots             = document.querySelectorAll('.color-dot');
  const pickerColor           = document.getElementById('pickerColorPersonalizado');
  const selectGrosor          = document.getElementById('selectGrosor');
  const btnDeshacer           = document.getElementById('btnDeshacer');
  const btnRehacer            = document.getElementById('btnRehacer');
  const btnExportarPng        = document.getElementById('btnExportarPng');
  const btnAbrirModal         = document.getElementById('btnAbrirModalGuardar');
  const formGuardar           = document.getElementById('formGuardarPatron');
  const modalElement          = document.getElementById('modalGuardarPatron');
  const btnNuevoProyecto       = document.getElementById('btnNuevoProyecto');
  const btnAbrirModalProyectos = document.getElementById('btnAbrirModalProyectos');
  const modalProyectosElement  = document.getElementById('modalElegirProyecto');
  const btnNuevoDesdeModal     = document.getElementById('btnNuevoDesdeModal');
  const inputFiltroProyectos   = document.getElementById('filtroProyectosModal');

  // 2. CONFIGURACIÓN Y ESTADO DE DIBUJO
  const PIXELS_PER_CM = 10; // 10 px = 1 cm en escala de patronaje

  const state = {
    activeTool  : 'pen',
    isDrawing   : false,
    startX      : 0, 
    startY      : 0,
    lastX       : 0, 
    lastY       : 0,
    snapshot    : null,
    strokeColor : '#ffffff',
    strokeWidth : 2,
    eraserSize  : 24,

    // Lógica Curva Bézier Cuadrática en 3 pasos
    curveStep   : 0, // 0: reposo, 1: arrastre a fin, 2: control magnético
    curveEndX   : 0,
    curveEndY   : 0,
    curveCpX    : 0,
    curveCpY    : 0,

    // Historial para Deshacer / Rehacer
    historyStack: [],
    historyIndex: -1,
    maxHistory  : 30
  };

  // 3. HELPERS DE MODAL SEGUROS (CON O SIN BOOTSTRAP DISPONIBLE)
  function abrirModalGuardar() {
    if (window.bootstrap && bootstrap.Modal) {
      const bsModal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
      bsModal.show();
    } else {
      modalElement.classList.add('show');
      modalElement.style.display = 'block';
      let backdrop = document.getElementById('modalCustomBackdrop');
      if (!backdrop) {
        backdrop = document.createElement('div');
        backdrop.id = 'modalCustomBackdrop';
        backdrop.className = 'modal-backdrop fade show';
        document.body.appendChild(backdrop);
      }
    }
  }

  function cerrarModalGuardar() {
    if (window.bootstrap && bootstrap.Modal) {
      const bsModal = bootstrap.Modal.getInstance(modalElement);
      if (bsModal) bsModal.hide();
    }
    modalElement.classList.remove('show');
    modalElement.style.display = 'none';
    const backdrop = document.getElementById('modalCustomBackdrop');
    if (backdrop) backdrop.remove();
  }

  function abrirModalProyectos() {
    if (window.bootstrap && bootstrap.Modal) {
      const bsModal = bootstrap.Modal.getInstance(modalProyectosElement) || new bootstrap.Modal(modalProyectosElement);
      bsModal.show();
    } else {
      modalProyectosElement.classList.add('show');
      modalProyectosElement.style.display = 'block';
      let backdrop = document.getElementById('modalCustomBackdropProyectos');
      if (!backdrop) {
        backdrop = document.createElement('div');
        backdrop.id = 'modalCustomBackdropProyectos';
        backdrop.className = 'modal-backdrop fade show';
        document.body.appendChild(backdrop);
      }
    }
  }

  function cerrarModalProyectos() {
    if (window.bootstrap && bootstrap.Modal) {
      const bsModal = bootstrap.Modal.getInstance(modalProyectosElement);
      if (bsModal) bsModal.hide();
    }
    modalProyectosElement.classList.remove('show');
    modalProyectosElement.style.display = 'none';
    const backdrop = document.getElementById('modalCustomBackdropProyectos');
    if (backdrop) backdrop.remove();
  }

  // Asignar cierre manual a botones de cerrar modal
  document.querySelectorAll('.btn-cerrar-modal').forEach(btn => {
    btn.addEventListener('click', cerrarModalGuardar);
  });

  document.querySelectorAll('.btn-cerrar-modal-proyectos').forEach(btn => {
    btn.addEventListener('click', cerrarModalProyectos);
  });

  // 4. INICIALIZACIÓN Y REDIMENSIONADO DEL CANVAS
  function initCanvas(restoreContent = true) {
    const rect = wrapper.getBoundingClientRect();
    const w = Math.floor(rect.width);
    const h = Math.floor(rect.height);

    if (canvas.width === w && canvas.height === h) return;

    let tempImgData = null;
    if (restoreContent && canvas.width > 0 && canvas.height > 0) {
      try {
        tempImgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
      } catch (e) {}
    }

    canvas.width  = w;
    canvas.height = h;

    aplicarEstilosContexto();

    if (tempImgData) {
      ctx.putImageData(tempImgData, 0, 0);
    } else {
      pushHistorial();
    }
  }

  function aplicarEstilosContexto() {
    ctx.strokeStyle = state.strokeColor;
    ctx.lineWidth   = state.strokeWidth;
    ctx.lineCap     = 'round';
    ctx.lineJoin    = 'round';
  }

  function getCanvasCoords(event) {
    const rect = canvas.getBoundingClientRect();
    return {
      x: Math.round(event.clientX - rect.left),
      y: Math.round(event.clientY - rect.top)
    };
  }

  function saveSnapshot() {
    state.snapshot = ctx.getImageData(0, 0, canvas.width, canvas.height);
  }

  function restoreSnapshot() {
    if (state.snapshot) {
      ctx.putImageData(state.snapshot, 0, 0);
    }
  }

  // 5. PILA DE HISTORIAL (DESHACER / REHACER)
  function pushHistorial() {
    if (state.historyIndex < state.historyStack.length - 1) {
      state.historyStack = state.historyStack.slice(0, state.historyIndex + 1);
    }

    state.historyStack.push(ctx.getImageData(0, 0, canvas.width, canvas.height));
    if (state.historyStack.length > state.maxHistory) {
      state.historyStack.shift();
    }
    state.historyIndex = state.historyStack.length - 1;
    actualizarBotonesHistorial();
  }

  function deshacer() {
    if (state.historyIndex > 0) {
      state.historyIndex--;
      ctx.putImageData(state.historyStack[state.historyIndex], 0, 0);
      actualizarBotonesHistorial();
    }
  }

  function rehacer() {
    if (state.historyIndex < state.historyStack.length - 1) {
      state.historyIndex++;
      ctx.putImageData(state.historyStack[state.historyIndex], 0, 0);
      actualizarBotonesHistorial();
    }
  }

  function actualizarBotonesHistorial() {
    btnDeshacer.style.opacity = state.historyIndex > 0 ? '1' : '0.4';
    btnRehacer.style.opacity = state.historyIndex < state.historyStack.length - 1 ? '1' : '0.4';
  }

  // 6. EVENTOS DE RATÓN (MOUSE DOWN, MOVE, UP)
  function onMouseDown(e) {
    const { x, y } = getCanvasCoords(e);

    // Herramienta Texto
    if (state.activeTool === 'text') {
      e.preventDefault();
      crearInputTextoFlotante(e, x, y);
      return;
    }

    // Herramienta Curva Bézier
    if (state.activeTool === 'curve') {
      if (state.curveStep === 0) {
        state.startX    = x;
        state.startY    = y;
        state.isDrawing = true;
        saveSnapshot();
        state.curveStep = 1;
      } else if (state.curveStep === 2) {
        restoreSnapshot();
        ctx.beginPath();
        ctx.moveTo(state.startX, state.startY);
        ctx.quadraticCurveTo(state.curveCpX, state.curveCpY, state.curveEndX, state.curveEndY);
        ctx.stroke();

        state.curveStep = 0;
        state.snapshot  = null;
        pushHistorial();
      }
      return;
    }

    state.isDrawing = true;
    state.startX = x;
    state.startY = y;
    state.lastX  = x;
    state.lastY  = y;

    if (['line', 'rect', 'ellipse'].includes(state.activeTool)) {
      saveSnapshot();
    }

    if (state.activeTool === 'pen') {
      ctx.beginPath();
      ctx.moveTo(x, y);
    }
  }

  function onMouseMove(e) {
    const { x, y } = getCanvasCoords(e);

    // Actualizar coordenadas en barra inferior
    spanCoordX.textContent  = x;
    spanCoordY.textContent  = y;
    spanCoordCm.textContent = `${(x / PIXELS_PER_CM).toFixed(1)}, ${(y / PIXELS_PER_CM).toFixed(1)}`;

    // Curva Bézier en paso 2 (control dinámico con cursor libre)
    if (state.activeTool === 'curve' && state.curveStep === 2) {
      state.curveCpX = x;
      state.curveCpY = y;
      drawCurvePreview();
      return;
    }

    if (!state.isDrawing) return;

    // Curva Bézier en paso 1 (arrastre hasta punto final)
    if (state.activeTool === 'curve' && state.curveStep === 1) {
      restoreSnapshot();
      ctx.beginPath();
      ctx.moveTo(state.startX, state.startY);
      ctx.lineTo(x, y);
      ctx.stroke();
      return;
    }

    // Renderizado según herramienta activa
    switch (state.activeTool) {
      case 'pen':     drawPen(x, y); break;
      case 'line':    drawLine(x, y, e.shiftKey); break;
      case 'rect':    drawRect(x, y); break;
      case 'ellipse': drawEllipse(x, y); break;
      case 'eraser':  drawEraser(x, y); break;
    }
  }

  function onMouseUp(e) {
    if (state.activeTool === 'curve' && state.curveStep === 1) {
      const { x, y } = getCanvasCoords(e);
      state.curveEndX = x;
      state.curveEndY = y;
      state.curveCpX  = (state.startX + x) / 2;
      state.curveCpY  = (state.startY + y) / 2;
      state.isDrawing = false;
      state.curveStep = 2;
      saveSnapshot();
      return;
    }

    if (!state.isDrawing) return;

    state.isDrawing = false;
    state.snapshot  = null;

    if (state.activeTool === 'pen' || state.activeTool === 'eraser') {
      ctx.closePath();
    }

    pushHistorial();
  }

  // 7. FUNCIONES DE DIBUJO ESPECÍFICAS
  function drawPen(x, y) {
    ctx.lineTo(x, y);
    ctx.stroke();
    state.lastX = x;
    state.lastY = y;
  }

  function drawLine(x, y, ortho = false) {
    if (ortho) {
      const snapped = snapOrtho(state.startX, state.startY, x, y);
      x = snapped.x;
      y = snapped.y;
    }

    restoreSnapshot();
    ctx.beginPath();
    ctx.moveTo(state.startX, state.startY);
    ctx.lineTo(x, y);
    ctx.stroke();

    // Medición flotante en centímetros
    const dx = x - state.startX;
    const dy = y - state.startY;
    const distPx = Math.hypot(dx, dy);

    if (distPx >= 6) {
      const distCm = (distPx / PIXELS_PER_CM).toFixed(1);
      const midX   = (state.startX + x) / 2;
      const midY   = (state.startY + y) / 2;

      ctx.save();
      ctx.font         = 'bold 12px Montserrat, sans-serif';
      ctx.textAlign    = 'center';
      ctx.textBaseline = 'bottom';

      const label = `${distCm} cm`;
      const textW = ctx.measureText(label).width + 12;
      const textH = 20;

      ctx.fillStyle = 'rgba(10, 6, 25, 0.85)';
      ctx.fillRect(midX - textW / 2, midY - textH - 2, textW, textH);
      ctx.strokeStyle = 'rgba(124, 58, 237, 0.6)';
      ctx.strokeRect(midX - textW / 2, midY - textH - 2, textW, textH);

      ctx.fillStyle = '#00f2fe';
      ctx.fillText(label, midX, midY - 4);
      ctx.restore();
    }
  }

  function drawRect(x, y) {
    restoreSnapshot();
    ctx.beginPath();
    ctx.strokeRect(state.startX, state.startY, x - state.startX, y - state.startY);
  }

  function drawEllipse(x, y) {
    restoreSnapshot();
    const cx = (state.startX + x) / 2;
    const cy = (state.startY + y) / 2;
    const rx = Math.abs(x - state.startX) / 2;
    const ry = Math.abs(y - state.startY) / 2;
    if (rx < 1 || ry < 1) return;
    ctx.beginPath();
    ctx.ellipse(cx, cy, rx, ry, 0, 0, Math.PI * 2);
    ctx.stroke();
  }

  function drawEraser(x, y) {
    const half = state.eraserSize / 2;
    ctx.clearRect(x - half, y - half, state.eraserSize, state.eraserSize);
  }

  function clearCanvas() {
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        title: '¿Limpiar todo el lienzo?',
        text: 'Se borrará el dibujo actual. Podrás deshacerlo con Ctrl+Z.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#4b5563',
        confirmButtonText: 'Sí, limpiar',
        cancelButtonText: 'Cancelar',
        background: '#0f0a21',
        color: '#ffffff'
      }).then((result) => {
        if (result.isConfirmed) {
          ctx.clearRect(0, 0, canvas.width, canvas.height);
          pushHistorial();
        }
      });
    } else {
      if (confirm('¿Limpiar todo el lienzo? Se borrará el dibujo actual.')) {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        pushHistorial();
      }
    }
  }

  function snapOrtho(x0, y0, x, y) {
    const dx = x - x0;
    const dy = y - y0;
    const dist = Math.hypot(dx, dy);
    const angleDeg = Math.atan2(dy, dx) * (180 / Math.PI);
    const snappedDeg = Math.round(angleDeg / 45) * 45;
    const snappedRad = snappedDeg * (Math.PI / 180);
    return {
      x: Math.round(x0 + dist * Math.cos(snappedRad)),
      y: Math.round(y0 + dist * Math.sin(snappedRad))
    };
  }

  function drawCurvePreview() {
    restoreSnapshot();

    ctx.beginPath();
    ctx.moveTo(state.startX, state.startY);
    ctx.quadraticCurveTo(state.curveCpX, state.curveCpY, state.curveEndX, state.curveEndY);
    ctx.stroke();

    ctx.save();
    ctx.setLineDash([4, 4]);
    ctx.strokeStyle = 'rgba(0, 242, 254, 0.5)';
    ctx.lineWidth   = 1;

    ctx.beginPath();
    ctx.moveTo(state.startX, state.startY);
    ctx.lineTo(state.curveCpX, state.curveCpY);
    ctx.lineTo(state.curveEndX, state.curveEndY);
    ctx.stroke();

    ctx.setLineDash([]);

    ctx.beginPath();
    ctx.arc(state.curveCpX, state.curveCpY, 5, 0, Math.PI * 2);
    ctx.fillStyle   = '#00f2fe';
    ctx.fill();
    ctx.strokeStyle = '#ffffff';
    ctx.lineWidth   = 1.5;
    ctx.stroke();

    ctx.restore();
  }

  function crearInputTextoFlotante(e, canvasX, canvasY) {
    const wrapperRect = wrapper.getBoundingClientRect();
    const inputLeft   = e.clientX - wrapperRect.left;
    const inputTop    = e.clientY - wrapperRect.top;

    const textInput = document.createElement('input');
    textInput.type  = 'text';
    Object.assign(textInput.style, {
      position     : 'absolute',
      left         : `${inputLeft}px`,
      top          : `${inputTop}px`,
      minWidth     : '140px',
      background   : '#120b29',
      color        : state.strokeColor,
      border       : 'none',
      borderBottom : '2px solid #7c3aed',
      outline      : 'none',
      fontSize     : '15px',
      fontFamily   : 'Montserrat, sans-serif',
      fontWeight   : '700',
      padding      : '2px 6px',
      zIndex       : '50',
      caretColor   : '#00f2fe'
    });

    wrapper.appendChild(textInput);
    setTimeout(() => textInput.focus(), 20);

    let committed = false;
    function commitText() {
      if (committed) return;
      committed = true;

      const texto = textInput.value.trim();
      if (texto !== '') {
        ctx.save();
        ctx.font         = 'bold 15px Montserrat, sans-serif';
        ctx.fillStyle    = state.strokeColor;
        ctx.textBaseline = 'top';
        ctx.fillText(texto, canvasX, canvasY);
        ctx.restore();
        pushHistorial();
      }

      if (textInput.parentNode) {
        textInput.parentNode.removeChild(textInput);
      }
    }

    textInput.addEventListener('keydown', (ev) => {
      if (ev.key === 'Enter') {
        ev.preventDefault();
        commitText();
      }
      if (ev.key === 'Escape') {
        committed = true;
        if (textInput.parentNode) textInput.parentNode.removeChild(textInput);
      }
    });

    textInput.addEventListener('blur', commitText);
  }

  // 8. CONTROLADORES DE HERRAMIENTA
  function setActiveTool(toolName) {
    if (toolName === 'clear') {
      clearCanvas();
      return;
    }

    if (state.activeTool === 'curve' && state.curveStep !== 0) {
      restoreSnapshot();
      state.curveStep = 0;
      state.isDrawing = false;
      state.snapshot  = null;
    }

    state.activeTool = toolName;

    toolButtons.forEach(btn => {
      const isActive = btn.dataset.tool === toolName;
      btn.classList.toggle('active', isActive);
      btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
    });

    const toolNames = {
      pen     : 'Pluma',
      line    : 'Línea recta (Shift 45°)',
      rect    : 'Rectángulo',
      ellipse : 'Elipse / Círculo',
      curve   : 'Curva Bézier',
      text    : 'Texto / Rotulación',
      select  : 'Seleccionar',
      eraser  : 'Borrador'
    };
    spanTool.textContent = toolNames[toolName] || toolName;

    canvas.className    = toolName === 'eraser' ? 'eraser-cursor' : '';
    canvas.style.cursor = toolName === 'select' ? 'default' :
                          toolName === 'text'   ? 'text' :
                          toolName === 'eraser' ? '' : 'crosshair';
  }

  // 9. SELECTORES DE COLOR Y GROSOR
  colorDots.forEach(dot => {
    dot.addEventListener('click', () => {
      colorDots.forEach(d => d.classList.remove('active'));
      dot.classList.add('active');
      state.strokeColor = dot.dataset.color;
      pickerColor.value = state.strokeColor;
      aplicarEstilosContexto();
    });
  });

  pickerColor.addEventListener('input', (e) => {
    colorDots.forEach(d => d.classList.remove('active'));
    state.strokeColor = e.target.value;
    aplicarEstilosContexto();
  });

  selectGrosor.addEventListener('change', (e) => {
    state.strokeWidth = parseInt(e.target.value, 10);
    aplicarEstilosContexto();
  });

  // 10. EXPORTAR IMAGEN PNG
  btnExportarPng.addEventListener('click', () => {
    const enlace = document.createElement('a');
    enlace.download = `patron_formai_${Date.now()}.png`;
    enlace.href = canvas.toDataURL('image/png');
    enlace.click();
  });

  // 11. GUARDAR EN LA BASE DE DATOS MEDIANTE AJAX
  btnAbrirModal.addEventListener('click', () => {
    const dataUrl = canvas.toDataURL('image/png');
    document.getElementById('guardarImagenBase64').value = dataUrl;

    const specJson = JSON.stringify({
      software: 'FormAI Pattern Studio',
      version: '1.0',
      ancho_px: canvas.width,
      alto_px: canvas.height,
      escala_cm: PIXELS_PER_CM,
      fecha: new Date().toISOString()
    });
    document.getElementById('guardarArchivoJson').value = specJson;

    abrirModalGuardar();
  });

  formGuardar.addEventListener('submit', async (e) => {
    e.preventDefault();

    const btnSubmit = document.getElementById('btnSubmitGuardar');
    btnSubmit.disabled = true;
    btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Guardando...';

    const checkNuevo = document.getElementById('checkGuardarComoNuevo');
    if (checkNuevo && checkNuevo.checked) {
      document.getElementById('guardarIdPatron').value = '0';
    }

    const formData = new FormData(formGuardar);

    try {
      const response = await fetch('/FromIA-HTML/dashboard.php?seccion=workspace', {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      });

      const rawText = await response.text();
      let res;
      try {
        res = JSON.parse(rawText);
      } catch (parseErr) {
        console.error("Respuesta cruda del servidor:", rawText);
        throw new Error("Respuesta no válida del servidor. Por favor intenta de nuevo.");
      }

      if (res.ok) {
        cerrarModalGuardar();
        document.getElementById('lblNombrePatron').textContent = document.getElementById('guardarNombre').value;
        if (res.id_patron) {
          document.getElementById('guardarIdPatron').value = res.id_patron;
          document.getElementById('lblBadgeEstado').textContent = `Proyecto #${res.id_patron}`;
        }

        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'success',
            title: '¡Guardado con éxito!',
            text: res.mensaje || 'Tu patrón ha sido guardado en tus proyectos.',
            background: '#0f0a21',
            color: '#ffffff',
            confirmButtonColor: '#7c3aed'
          });
        } else {
          alert('¡Guardado con éxito! ' + (res.mensaje || ''));
        }
      } else {
        throw new Error(res.mensaje || 'Error al guardar el proyecto');
      }
    } catch (err) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'error',
          title: 'No se pudo guardar',
          text: err.message || 'Ocurrió un inconveniente al guardar. Por favor intenta de nuevo.',
          background: '#0f0a21',
          color: '#ffffff',
          confirmButtonColor: '#ef4444'
        });
      } else {
        alert('Error: ' + (err.message || 'No se pudo guardar'));
      }
    } finally {
      btnSubmit.disabled = false;
      btnSubmit.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Confirmar y Guardar';
    }
  });

  // 12. CARGA DE IMAGEN PREVIA (SI SE ABRE UN PROYECTO EXISTENTE)
  const urlImagenInicial = "<?= $imagenInicial; ?>";
  if (urlImagenInicial !== "") {
    const imgPrevia = new Image();
    imgPrevia.crossOrigin = 'anonymous';
    imgPrevia.onload = () => {
      ctx.drawImage(imgPrevia, 0, 0);
      pushHistorial();
    };
    imgPrevia.src = urlImagenInicial;
  }

  // 13. FUNCIONES DE GESTIÓN DE PROYECTOS (NUEVO Y ABRIR)
  function iniciarNuevoProyecto() {
    const accionReiniciar = () => {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      state.historyStack = [];
      state.historyIndex = -1;
      pushHistorial();

      document.getElementById('lblNombrePatron').textContent = 'Patrón sin título';
      document.getElementById('lblBadgeEstado').textContent = 'Nuevo Proyecto';
      document.getElementById('guardarIdPatron').value = '0';
      document.getElementById('guardarNombre').value = 'Mi Nuevo Patrón';
      document.getElementById('guardarTipo').value = 'patronaje';
      document.getElementById('guardarDesc').value = '';

      const checkNuevo = document.getElementById('checkGuardarComoNuevo');
      if (checkNuevo) {
        checkNuevo.checked = false;
        const contenedor = checkNuevo.closest('.form-check');
        if (contenedor) contenedor.classList.add('d-none');
      }

      window.history.replaceState({}, document.title, '/FromIA-HTML/dashboard.php?seccion=workspace');

      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'success',
          title: 'Lienzo en Blanco',
          text: 'Comienza tu nuevo patrón y guárdalo cuando lo desees.',
          timer: 1600,
          showConfirmButton: false,
          background: '#0f0a21',
          color: '#ffffff'
        });
      }
    };

    if (state.historyIndex > 0) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          title: '¿Iniciar un nuevo proyecto?',
          text: 'Se reiniciará el lienzo para crear un patrón desde cero. Asegúrate de guardar los cambios actuales.',
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#7c3aed',
          cancelButtonColor: '#4b5563',
          confirmButtonText: 'Sí, crear nuevo',
          cancelButtonText: 'Cancelar',
          background: '#0f0a21',
          color: '#ffffff'
        }).then((result) => {
          if (result.isConfirmed) accionReiniciar();
        });
      } else {
        if (confirm('¿Iniciar un nuevo proyecto? Se reiniciará el lienzo actual.')) {
          accionReiniciar();
        }
      }
    } else {
      accionReiniciar();
    }
  }

  // 14. REGISTRO DE LISTENERS DE BOTONES Y CANVAS
  btnNuevoProyecto.addEventListener('click', (e) => {
    e.preventDefault();
    iniciarNuevoProyecto();
  });

  btnAbrirModalProyectos.addEventListener('click', (e) => {
    e.preventDefault();
    abrirModalProyectos();
  });

  if (btnNuevoDesdeModal) {
    btnNuevoDesdeModal.addEventListener('click', (e) => {
      e.preventDefault();
      cerrarModalProyectos();
      iniciarNuevoProyecto();
    });
  }

  // Buscador de proyectos en tiempo real dentro del modal
  if (inputFiltroProyectos) {
    inputFiltroProyectos.addEventListener('input', (e) => {
      const q = e.target.value.toLowerCase().trim();
      const items = document.querySelectorAll('.item-proyecto-modal');
      let encontrados = 0;
      items.forEach(item => {
        const nomb = item.getAttribute('data-nombre') || '';
        const tipo = item.getAttribute('data-tipo') || '';
        const visible = nomb.includes(q) || tipo.includes(q);
        item.style.display = visible ? 'block' : 'none';
        if (visible) encontrados++;
      });
      const noRes = document.getElementById('noResultadosFiltro');
      if (noRes) noRes.classList.toggle('d-none', encontrados > 0);
    });
  }

  // Abrir proyecto existente seleccionado en el modal
  document.querySelectorAll('.btn-cargar-proyecto').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const id = btn.getAttribute('data-id');
      const nomb = btn.getAttribute('data-nombre') || 'Proyecto';
      if (!id) return;

      const cargar = () => {
        window.location.href = `/FromIA-HTML/dashboard.php?seccion=workspace&id_patron=${id}`;
      };

      if (state.historyIndex > 0) {
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            title: `¿Abrir "${nomb}"?`,
            text: 'Se reemplazará el lienzo con el proyecto seleccionado. Recuerda guardar tus cambios actuales.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#7c3aed',
            cancelButtonColor: '#4b5563',
            confirmButtonText: 'Sí, abrir proyecto',
            cancelButtonText: 'Cancelar',
            background: '#0f0a21',
            color: '#ffffff'
          }).then((res) => {
            if (res.isConfirmed) cargar();
          });
        } else {
          if (confirm(`¿Abrir "${nomb}"? Se reemplazará el lienzo con el proyecto seleccionado.`)) {
            cargar();
          }
        }
      } else {
        cargar();
      }
    });
  });

  canvas.addEventListener('mousedown', onMouseDown);
  canvas.addEventListener('mousemove', onMouseMove);
  canvas.addEventListener('mouseup', onMouseUp);
  canvas.addEventListener('mouseleave', onMouseUp);

  toolButtons.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const toolName = btn.getAttribute('data-tool');
      if (toolName) setActiveTool(toolName);
    });
  });

  btnDeshacer.addEventListener('click', (e) => {
    e.preventDefault();
    deshacer();
  });

  btnRehacer.addEventListener('click', (e) => {
    e.preventDefault();
    rehacer();
  });

  // Atajos de teclado
  window.addEventListener('keydown', (e) => {
    if (['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) return;

    if (e.ctrlKey && e.key.toLowerCase() === 'z') {
      e.preventDefault();
      if (e.shiftKey) rehacer(); else deshacer();
    } else if (e.ctrlKey && e.key.toLowerCase() === 'y') {
      e.preventDefault();
      rehacer();
    } else if (e.key.toLowerCase() === 'p') {
      setActiveTool('pen');
    } else if (e.key.toLowerCase() === 'l') {
      setActiveTool('line');
    } else if (e.key.toLowerCase() === 'r') {
      setActiveTool('rect');
    } else if (e.key.toLowerCase() === 'o') {
      setActiveTool('ellipse');
    } else if (e.key.toLowerCase() === 'c') {
      setActiveTool('curve');
    } else if (e.key.toLowerCase() === 't') {
      setActiveTool('text');
    } else if (e.key.toLowerCase() === 'e') {
      setActiveTool('eraser');
    }
  });

  // 15. ADAPTACIÓN AL TAMAÑO DE PANTALLA
  const resizeObserver = new ResizeObserver(() => initCanvas(true));
  resizeObserver.observe(wrapper);

  initCanvas(false);
  console.log("✅ Aplicación de Patronaje FormAI lista para interactuar.");
})();
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
