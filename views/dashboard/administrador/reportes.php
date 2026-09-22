<?php
$pageTitle        = "FormAI - Reportes y Estadísticas";
$extraCss         = "dashboard.css";
$nombre           = $nombre ?? ($_SESSION['user_nombre'] ?? 'Administrador');
$tipoReporte      = $tipoReporte ?? 'ingresos';
$fechaInicio      = $fechaInicio ?? date('Y-01-01');
$fechaFin         = $fechaFin ?? date('Y-m-d');
$reporteIngresos  = $reporteIngresos ?? [];
$transacciones    = $transacciones ?? [];
$topClientes      = $topClientes ?? [];
$usuariosReporte  = $usuariosReporte ?? [];
$proyectosReporte = $proyectosReporte ?? [];
$totalRango       = $totalRango ?? 0.0;
$pagosRango       = $pagosRango ?? 0;
$totalIngresos    = $totalIngresos ?? 0.0;
$totalPagos       = $totalPagos ?? 0;
$promedioRango    = $promedioRango ?? 0.0;
$maxIngresoDia    = $maxIngresoDia ?? 0.0;

require_once __DIR__ . '/../../layouts/header.php';
?>

<!-- Biblioteca para exportación PDF directa en el cliente -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<div class="dashboard-wrapper">
  <!-- Sidebar Administrador -->
  <aside class="sidebar-custom">
    <div>
      <div class="sidebar-group-title">GESTIÓN GENERAL</div>
      <ul class="sidebar-menu">
        <li><a href="/FromIA-HTML/dashboard.php"><i class="bi bi-speedometer2"></i> Métricas Globales</a></li>
        <li><a href="/FromIA-HTML/dashboard.php?seccion=usuarios"><i class="bi bi-people-fill"></i> Usuarios del Sistema</a></li>
        <li><a href="/FromIA-HTML/dashboard.php?seccion=pagos"><i class="bi bi-credit-card-fill"></i> Historial de Pagos</a></li>
        <li><a href="/FromIA-HTML/dashboard.php?seccion=reportes" class="active"><i class="bi bi-file-earmark-bar-graph"></i> Reportes</a></li>
      </ul>
    </div>
  </aside>

  <!-- Contenido Principal -->
  <main class="main-panel">
    <!-- Cabecera y Botones de Acción -->
    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
      <div>
        <div class="d-flex align-items-center gap-2">
          <h1 class="welcome-title mb-1">Centro de Reportes del Sistema 📊</h1>
          <span id="badge-live-status" class="badge" style="background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); font-size: 0.75rem;">
            <i class="bi bi-broadcast me-1"></i> Tiempo Real Activo
          </span>
        </div>
        <p class="welcome-subtitle mb-0">Selecciona o cambia el rango de fechas para actualizar los datos y gráficos <strong>en tiempo real</strong>.</p>
      </div>

      <!-- Barra de Exportación Directa -->
      <div class="d-flex align-items-center gap-2 flex-wrap">
        <!-- Exportar a Excel (.xls) -->
        <a id="btn-exportar-excel" 
           href="/FromIA-HTML/dashboard.php?seccion=reportes&tipo_reporte=<?= urlencode($tipoReporte); ?>&fecha_inicio=<?= urlencode($fechaInicio); ?>&fecha_fin=<?= urlencode($fechaFin); ?>&exportar=excel" 
           class="btn btn-sm d-flex align-items-center gap-2" 
           style="background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.4); border-radius: 8px; padding: 8px 14px; font-weight: 600;"
           title="Descargar libro de cálculo estructurado para Microsoft Excel">
          <i class="bi bi-file-earmark-excel-fill text-success" style="font-size: 1.1rem;"></i>
          <span>Exportar a Excel (.xls)</span>
        </a>

        <!-- Exportar a CSV -->
        <a id="btn-exportar-csv" 
           href="/FromIA-HTML/dashboard.php?seccion=reportes&tipo_reporte=<?= urlencode($tipoReporte); ?>&fecha_inicio=<?= urlencode($fechaInicio); ?>&fecha_fin=<?= urlencode($fechaFin); ?>&exportar=csv" 
           class="btn btn-sm d-flex align-items-center gap-2" 
           style="background: rgba(14, 165, 233, 0.15); color: #38bdf8; border: 1px solid rgba(14, 165, 233, 0.4); border-radius: 8px; padding: 8px 14px; font-weight: 600;"
           title="Descargar archivo plano CSV con codificación UTF-8">
          <i class="bi bi-filetype-csv text-info" style="font-size: 1.1rem;"></i>
          <span>Exportar CSV</span>
        </a>

        <!-- Descargar PDF (.pdf) -->
        <button id="btn-descargar-pdf" onclick="descargarReportePDF()" 
                class="btn btn-sm d-flex align-items-center gap-2" 
                style="background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.4); border-radius: 8px; padding: 8px 14px; font-weight: 600;"
                title="Generar y descargar documento PDF">
          <i class="bi bi-file-earmark-pdf-fill text-danger" style="font-size: 1.1rem;"></i>
          <span>Descargar PDF</span>
        </button>

        <!-- Imprimir -->
        <button onclick="window.print()" class="btn btn-outline-light btn-sm" style="border-color: rgba(255,255,255,0.2); border-radius: 8px; padding: 8px 12px;" title="Imprimir o guardar como PDF en navegador">
          <i class="bi bi-printer"></i>
        </button>
      </div>
    </div>

    <!-- Pestañas de Selección de Reporte -->
    <div class="mb-4">
      <ul class="nav nav-pills" style="gap: 8px;">
        <li class="nav-item">
          <a class="nav-link <?= $tipoReporte === 'ingresos' ? 'active' : ''; ?>" 
             href="/FromIA-HTML/dashboard.php?seccion=reportes&tipo_reporte=ingresos&fecha_inicio=<?= urlencode($fechaInicio); ?>&fecha_fin=<?= urlencode($fechaFin); ?>"
             style="background: <?= $tipoReporte === 'ingresos' ? '#7c3aed' : '#191132'; ?>; color: #fff; border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; padding: 10px 18px; font-weight: 500;">
            <i class="bi bi-cash-stack me-2"></i> Reporte de Ingresos y Transacciones
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $tipoReporte === 'usuarios' ? 'active' : ''; ?>" 
             href="/FromIA-HTML/dashboard.php?seccion=reportes&tipo_reporte=usuarios&fecha_inicio=<?= urlencode($fechaInicio); ?>&fecha_fin=<?= urlencode($fechaFin); ?>"
             style="background: <?= $tipoReporte === 'usuarios' ? '#7c3aed' : '#191132'; ?>; color: #fff; border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; padding: 10px 18px; font-weight: 500;">
            <i class="bi bi-people-fill me-2"></i> Reporte de Usuarios y Membresías
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $tipoReporte === 'proyectos' ? 'active' : ''; ?>" 
             href="/FromIA-HTML/dashboard.php?seccion=reportes&tipo_reporte=proyectos&fecha_inicio=<?= urlencode($fechaInicio); ?>&fecha_fin=<?= urlencode($fechaFin); ?>"
             style="background: <?= $tipoReporte === 'proyectos' ? '#7c3aed' : '#191132'; ?>; color: #fff; border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; padding: 10px 18px; font-weight: 500;">
            <i class="bi bi-cpu-fill me-2"></i> Reporte de Proyectos y Producción Digital
          </a>
        </li>
      </ul>
    </div>

    <!-- Filtro de Fechas y Rangos Rápidos -->
    <div class="projects-box mb-4 p-3" style="background: rgba(25, 17, 50, 0.7); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 14px;">
      <form id="form-fechas-reporte" method="GET" action="/FromIA-HTML/dashboard.php" class="row g-3 align-items-end" onsubmit="return false;">
        <input type="hidden" id="tipo_reporte" name="tipo_reporte" value="<?= htmlspecialchars($tipoReporte, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="seccion" value="reportes">
        
        <div class="col-md-3 col-6">
          <label class="form-label" style="font-size: 0.8rem; color: #a5b4fc; text-transform: uppercase; font-weight: 600;">
            <i class="bi bi-calendar-event me-1"></i> Fecha Desde
          </label>
          <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" 
                 value="<?= htmlspecialchars($fechaInicio, ENT_QUOTES, 'UTF-8'); ?>"
                 style="background: #1e153b; border-color: rgba(255,255,255,0.12); color: #fff;">
        </div>

        <div class="col-md-3 col-6">
          <label class="form-label" style="font-size: 0.8rem; color: #a5b4fc; text-transform: uppercase; font-weight: 600;">
            <i class="bi bi-calendar-check me-1"></i> Fecha Hasta
          </label>
          <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" 
                 value="<?= htmlspecialchars($fechaFin, ENT_QUOTES, 'UTF-8'); ?>"
                 style="background: #1e153b; border-color: rgba(255,255,255,0.12); color: #fff;">
        </div>

        <!-- Botones de Rangos Rápidos -->
        <div class="col-md-6 col-12">
          <label class="form-label d-block" style="font-size: 0.8rem; color: #94a3b8; text-transform: uppercase; font-weight: 600;">
            <i class="bi bi-lightning-charge me-1"></i> Rangos Rápidos
          </label>
          <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-sm btn-preset" onclick="aplicarPreset('hoy')" 
                    style="background: rgba(255,255,255,0.06); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.1); border-radius: 6px;">Hoy</button>
            <button type="button" class="btn btn-sm btn-preset" onclick="aplicarPreset('7dias')" 
                    style="background: rgba(255,255,255,0.06); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.1); border-radius: 6px;">Últimos 7 días</button>
            <button type="button" class="btn btn-sm btn-preset" onclick="aplicarPreset('30dias')" 
                    style="background: rgba(255,255,255,0.06); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.1); border-radius: 6px;">Últimos 30 días</button>
            <button type="button" class="btn btn-sm btn-preset" onclick="aplicarPreset('mes')" 
                    style="background: rgba(255,255,255,0.06); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.1); border-radius: 6px;">Este Mes</button>
            <button type="button" class="btn btn-sm btn-preset" onclick="aplicarPreset('2026')" 
                    style="background: rgba(124, 58, 237, 0.2); color: #c4b5fd; border: 1px solid rgba(124, 58, 237, 0.4); border-radius: 6px;">Año 2026</button>
            <button type="button" class="btn btn-sm btn-preset" onclick="aplicarPreset('todo')" 
                    style="background: rgba(255,255,255,0.06); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.1); border-radius: 6px;">Histórico</button>
          </div>
        </div>
      </form>
    </div>

    <!-- CONTENEDOR EXPORTABLE (Se actualiza dinámicamente vía AJAX en tiempo real y se exporta a PDF/Excel) -->
    <div class="position-relative">
      <!-- Indicador de carga flotante -->
      <div id="loading-overlay" class="d-none position-absolute top-50 start-50 translate-middle" style="z-index: 10;">
        <div class="d-flex align-items-center gap-2 px-3 py-2 rounded-pill shadow-lg" 
             style="background: rgba(15, 10, 33, 0.95); border: 1px solid rgba(124, 58, 237, 0.5); color: #c4b5fd; font-weight: 600;">
          <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
          <span>Sincronizando datos en tiempo real...</span>
        </div>
      </div>

      <div id="reporte-export-container" style="transition: opacity 0.2s ease;">
        <?php require __DIR__ . '/reportes_contenido.php'; ?>
      </div>
    </div>

    <!-- Tarjeta informativa de Vistas y Procedimientos Almacenados -->
    <div class="projects-box mt-4" style="background: rgba(20, 14, 40, 0.85); border: 1px solid rgba(124, 58, 237, 0.3);">
      <div class="projects-top">
        <h2 class="mb-1" style="color: #c4b5fd;"><i class="bi bi-database-check me-2"></i> Rutinas de MySQL Integradas en este Reporte</h2>
        <span style="font-size: 0.85rem; color: #a5b4fc;">Los datos tabulares y exportables son consultados directamente mediante las siguientes estructuras:</span>
      </div>

      <div class="row g-3 mt-2">
        <div class="col-md-6 col-12">
          <div class="p-3" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07); border-radius: 10px;">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <span class="fw-bold text-white"><i class="bi bi-gear-wide-connected text-primary me-2"></i> sp_reporte_ingresos</span>
              <span class="badge bg-success">Stored Procedure Activo</span>
            </div>
            <p class="text-secondary mb-0" style="font-size: 0.82rem;">
              Agrupa pagos completados por día entre fechas para el cálculo financiero rápido.
            </p>
          </div>
        </div>

        <div class="col-md-6 col-12">
          <div class="p-3" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07); border-radius: 10px;">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <span class="fw-bold text-white"><i class="bi bi-table text-info me-2"></i> v_resumen_pagos & v_usuarios</span>
              <span class="badge bg-info text-dark">Vistas Activas</span>
            </div>
            <p class="text-secondary mb-0" style="font-size: 0.82rem;">
              Proveen la información cruzada de planes, gasto acumulado de clientes y usuarios registrados.
            </p>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<!-- Scripts de Interacción en Tiempo Real y Generación de PDF -->
<script>
// Manejo de actualización en tiempo real al cambiar fechas
document.addEventListener('DOMContentLoaded', function() {
  const inputInicio = document.getElementById('fecha_inicio');
  const inputFin    = document.getElementById('fecha_fin');

  if (inputInicio) {
    inputInicio.addEventListener('change', dispararActualizacionEnTiempoReal);
  }
  if (inputFin) {
    inputFin.addEventListener('change', dispararActualizacionEnTiempoReal);
  }
});

let debounceTimer = null;

function dispararActualizacionEnTiempoReal() {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(ejecutarActualizacionAJAX, 250);
}

function ejecutarActualizacionAJAX() {
  const inputInicio = document.getElementById('fecha_inicio');
  const inputFin    = document.getElementById('fecha_fin');
  const tipoReporte = document.getElementById('tipo_reporte') ? document.getElementById('tipo_reporte').value : 'ingresos';

  const fechaInicio = inputInicio ? inputInicio.value : '';
  const fechaFin    = inputFin ? inputFin.value : '';

  if (!fechaInicio || !fechaFin) return;

  // Actualizar enlaces de exportación (Excel, CSV) en tiempo real
  actualizarEnlacesExportacion(tipoReporte, fechaInicio, fechaFin);

  // Mostrar estado visual de carga
  const container = document.getElementById('reporte-export-container');
  const overlay   = document.getElementById('loading-overlay');
  const badgeLive = document.getElementById('badge-live-status');

  if (container) container.style.opacity = '0.35';
  if (overlay) overlay.classList.remove('d-none');
  if (badgeLive) {
    badgeLive.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Actualizando...';
  }

  // Actualizar URL del navegador sin recargar para persistir el rango
  const nuevaUrl = `/FromIA-HTML/dashboard.php?seccion=reportes&tipo_reporte=${encodeURIComponent(tipoReporte)}&fecha_inicio=${encodeURIComponent(fechaInicio)}&fecha_fin=${encodeURIComponent(fechaFin)}`;
  window.history.replaceState({ path: nuevaUrl }, '', nuevaUrl);

  // Petición AJAX en tiempo real al backend
  fetch(`${nuevaUrl}&ajax=1`)
    .then(response => {
      if (!response.ok) throw new Error('Error en la respuesta del servidor');
      return response.json();
    })
    .then(data => {
      if (data.success && container) {
        container.innerHTML = data.html;
      }
    })
    .catch(err => {
      console.error('Error al actualizar en tiempo real:', err);
    })
    .finally(() => {
      if (container) container.style.opacity = '1';
      if (overlay) overlay.classList.add('d-none');
      if (badgeLive) {
        badgeLive.innerHTML = '<i class="bi bi-broadcast me-1"></i> Tiempo Real Activo';
      }
    });
}

function actualizarEnlacesExportacion(tipo, inicio, fin) {
  const btnExcel = document.getElementById('btn-exportar-excel');
  const btnCsv   = document.getElementById('btn-exportar-csv');

  if (btnExcel) {
    btnExcel.href = `/FromIA-HTML/dashboard.php?seccion=reportes&tipo_reporte=${encodeURIComponent(tipo)}&fecha_inicio=${encodeURIComponent(inicio)}&fecha_fin=${encodeURIComponent(fin)}&exportar=excel`;
  }
  if (btnCsv) {
    btnCsv.href = `/FromIA-HTML/dashboard.php?seccion=reportes&tipo_reporte=${encodeURIComponent(tipo)}&fecha_inicio=${encodeURIComponent(inicio)}&fecha_fin=${encodeURIComponent(fin)}&exportar=csv`;
  }
}

// Accesos directos de rangos rápidos
function aplicarPreset(preset) {
  const hoy = new Date();
  const formatoFecha = d => d.toISOString().split('T')[0];

  let fechaInicio = '';
  let fechaFin = formatoFecha(hoy);

  if (preset === 'hoy') {
    fechaInicio = fechaFin;
  } else if (preset === '7dias') {
    const d = new Date();
    d.setDate(d.getDate() - 7);
    fechaInicio = formatoFecha(d);
  } else if (preset === '30dias') {
    const d = new Date();
    d.setDate(d.getDate() - 30);
    fechaInicio = formatoFecha(d);
  } else if (preset === 'mes') {
    const d = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
    fechaInicio = formatoFecha(d);
  } else if (preset === '2026') {
    fechaInicio = '2026-01-01';
    fechaFin    = '2026-12-31';
  } else if (preset === 'todo') {
    fechaInicio = '2024-01-01';
  }

  const inputInicio = document.getElementById('fecha_inicio');
  const inputFin    = document.getElementById('fecha_fin');

  if (inputInicio) inputInicio.value = fechaInicio;
  if (inputFin) inputFin.value = fechaFin;

  ejecutarActualizacionAJAX();
}

// Descarga en formato PDF
function descargarReportePDF() {
  const container = document.getElementById('reporte-export-container');
  if (!container) return;

  const btn = document.getElementById('btn-descargar-pdf');
  const originalHtml = btn ? btn.innerHTML : '';
  if (btn) {
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Generando PDF...';
    btn.disabled = true;
  }

  const tipoReporte = document.getElementById('tipo_reporte') ? document.getElementById('tipo_reporte').value : 'ingresos';

  const opt = {
    margin: [8, 8, 8, 8],
    filename: `Reporte_FormAI_${tipoReporte}_${new Date().toISOString().slice(0,10)}.pdf`,
    image: { type: 'jpeg', quality: 0.98 },
    html2canvas: { scale: 2, useCORS: true, logging: false },
    jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
  };

  html2pdf().set(opt).from(container).save().then(() => {
    if (btn) {
      btn.innerHTML = originalHtml;
      btn.disabled = false;
    }
  }).catch((err) => {
    console.error('Error al generar PDF con html2pdf:', err);
    if (btn) {
      btn.innerHTML = originalHtml;
      btn.disabled = false;
    }
    window.print();
  });
}
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
