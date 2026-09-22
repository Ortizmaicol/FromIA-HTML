<?php
$pageTitle     = "FormAI - Mis Proyectos";
$extraCss      = "dashboard.css";
$nombre        = $nombre ?? ($_SESSION['user_nombre'] ?? 'Cliente');
$proyectos     = $proyectos ?? [];
$totalActivos  = $totalActivos ?? 0;
$totalProyectos= $totalProyectos ?? count($proyectos);
$mensajeExito  = $mensajeExito ?? null;
$mensajeError  = $mensajeError ?? null;

// Conteo por tipo para las pestañas
$conteoTipos = [
    'todos'     => count($proyectos),
    'patronaje' => 0,
    'diseño'    => 0,
    '3d'        => 0,
    'mixto'     => 0,
    'archivado' => 0
];
foreach ($proyectos as $pr) {
    $t = strtolower($pr['tipo_patron'] ?? 'patronaje');
    if (isset($conteoTipos[$t])) {
        $conteoTipos[$t]++;
    }
    if (($pr['estado_patron'] ?? '') === 'archivado') {
        $conteoTipos['archivado']++;
    }
}

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
        <li><a href="/FromIA-HTML/dashboard.php?seccion=membresia"><i class="bi bi-gem"></i> Mi Membresía</a></li>
        <li><a href="/FromIA-HTML/dashboard.php?seccion=proyectos" class="active"><i class="bi bi-folder-fill"></i> Mis Proyectos <span class="sidebar-badge-count"><?= (int)$totalActivos; ?></span></a></li>
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

  <!-- Contenido Principal: Mis Proyectos -->
  <main class="main-panel">
    
    <!-- Encabezado con Botón de Acción -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
      <div>
        <h1 class="welcome-title mb-1">Mis Proyectos 📁</h1>
        <p class="welcome-subtitle mb-0">Gestiona, edita y organiza tus patrones, diseños 3D y trazados textiles.</p>
      </div>
      <div>
        <button type="button" class="btn-formai-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoProyecto">
          <i class="bi bi-plus-lg fs-5"></i>
          <span>Nuevo Proyecto</span>
        </button>
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

    <!-- Barra de Búsqueda y Pestañas de Filtro -->
    <div class="mb-4">
      <div class="row g-3 align-items-center mb-3">
        <div class="col-12 col-md-6">
          <div class="input-group">
            <span class="input-group-text formai-input-dark" style="border-right: none; color: #8a87a2;">
              <i class="bi bi-search"></i>
            </span>
            <input type="text" id="buscadorProyectos" class="form-control formai-input-dark" placeholder="Buscar por nombre o descripción..." style="border-left: none;" />
          </div>
        </div>
      </div>

      <!-- Filtros por tipo y estado -->
      <div class="filter-tabs">
        <button type="button" class="filter-pill active" data-filter="todos">
          Todos <span class="badge rounded-pill bg-dark ms-1"><?= $conteoTipos['todos']; ?></span>
        </button>
        <button type="button" class="filter-pill" data-filter="patronaje">
          <i class="bi bi-scissors me-1"></i> Patronaje <span class="badge rounded-pill bg-dark ms-1"><?= $conteoTipos['patronaje']; ?></span>
        </button>
        <button type="button" class="filter-pill" data-filter="diseño">
          <i class="bi bi-palette-fill me-1"></i> Diseño <span class="badge rounded-pill bg-dark ms-1"><?= $conteoTipos['diseño']; ?></span>
        </button>
        <button type="button" class="filter-pill" data-filter="3d">
          <i class="bi bi-box-seam-fill me-1"></i> 3D <span class="badge rounded-pill bg-dark ms-1"><?= $conteoTipos['3d']; ?></span>
        </button>
        <button type="button" class="filter-pill" data-filter="mixto">
          <i class="bi bi-layers-fill me-1"></i> Mixto <span class="badge rounded-pill bg-dark ms-1"><?= $conteoTipos['mixto']; ?></span>
        </button>
        <button type="button" class="filter-pill" data-filter="archivado">
          <i class="bi bi-archive-fill me-1"></i> Archivados <span class="badge rounded-pill bg-dark ms-1"><?= $conteoTipos['archivado']; ?></span>
        </button>
      </div>
    </div>

    <!-- Lista / Cuadrícula de Proyectos -->
    <div id="contenedorProyectos" class="projects-grid-cards">
      <?php if (!empty($proyectos)): ?>
        <?php foreach ($proyectos as $p): 
          $tipoClass = strtolower($p['tipo_patron']);
          if ($tipoClass === '3d') $tipoClass = 't3d';
          $iconClass = 'bi-scissors';
          if ($p['tipo_patron'] === 'diseño') $iconClass = 'bi-palette-fill';
          if ($p['tipo_patron'] === '3d') $iconClass = 'bi-box-seam-fill';
          if ($p['tipo_patron'] === 'mixto') $iconClass = 'bi-layers-fill';

          $tieneArchivo = !empty($p['ubicacion']) || (!empty($p['archivo_json']) && $p['archivo_json'] !== '{}');
        ?>
          <div class="project-card tarjeta-proyecto" 
               data-id="<?= (int)$p['id_patron']; ?>"
               data-nombre="<?= htmlspecialchars(strtolower($p['nomb_patron']), ENT_QUOTES, 'UTF-8'); ?>"
               data-desc="<?= htmlspecialchars(strtolower($p['desc_patron'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
               data-tipo="<?= htmlspecialchars(strtolower($p['tipo_patron']), ENT_QUOTES, 'UTF-8'); ?>"
               data-estado="<?= htmlspecialchars(strtolower($p['estado_patron']), ENT_QUOTES, 'UTF-8'); ?>">
            
            <div>
              <div class="project-card-header">
                <div class="project-type-icon <?= $tipoClass; ?>">
                  <i class="bi <?= $iconClass; ?>"></i>
                </div>
                <div>
                  <?php if ($p['estado_patron'] === 'activo'): ?>
                    <span class="stat-pill stat-pill-green">
                      <i class="bi bi-check-circle-fill me-1"></i> Activo
                    </span>
                  <?php else: ?>
                    <span class="stat-pill" style="background: rgba(243, 156, 18, 0.15); color: #f39c12; border: 1px solid rgba(243, 156, 18, 0.3);">
                      <i class="bi bi-archive me-1"></i> Archivado
                    </span>
                  <?php endif; ?>
                </div>
              </div>

              <h3 class="project-card-title"><?= htmlspecialchars($p['nomb_patron'], ENT_QUOTES, 'UTF-8'); ?></h3>
              
              <p class="project-card-desc">
                <?php if (!empty($p['desc_patron'])): ?>
                  <?= nl2br(htmlspecialchars($p['desc_patron'], ENT_QUOTES, 'UTF-8')); ?>
                <?php else: ?>
                  <span style="color: #615e78; font-style: italic;">Sin descripción detallada.</span>
                <?php endif; ?>
              </p>
            </div>

            <div>
              <div class="d-flex align-items-center justify-content-between mb-3 text-muted" style="font-size: 0.78rem;">
                <span>
                  <i class="bi bi-calendar3 me-1"></i> <?= date('d/m/Y', strtotime($p['fecha_creacion'])); ?>
                </span>
                <span class="badge bg-dark text-info text-capitalize" style="border: 1px solid rgba(255,255,255,0.08);">
                  <?= htmlspecialchars($p['tipo_patron']); ?>
                </span>
              </div>

              <div class="project-card-footer">
                <div class="d-flex align-items-center gap-1">
                  <!-- Botón Editar -->
                  <button type="button" class="btn btn-sm btn-outline-light btn-editar-proyecto" 
                          data-bs-toggle="modal" 
                          data-bs-target="#modalEditarProyecto"
                          data-id="<?= (int)$p['id_patron']; ?>"
                          data-nombre="<?= htmlspecialchars($p['nomb_patron'], ENT_QUOTES, 'UTF-8'); ?>"
                          data-desc="<?= htmlspecialchars($p['desc_patron'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                          data-tipo="<?= htmlspecialchars($p['tipo_patron'], ENT_QUOTES, 'UTF-8'); ?>"
                          data-estado="<?= htmlspecialchars($p['estado_patron'], ENT_QUOTES, 'UTF-8'); ?>"
                          title="Editar información">
                    <i class="bi bi-pencil-fill"></i>
                  </button>

                  <!-- Botón Archivar/Reactivar -->
                  <form method="POST" action="/FromIA-HTML/dashboard.php?seccion=proyectos" class="d-inline">
                    <input type="hidden" name="accion" value="cambiar_estado_patron">
                    <input type="hidden" name="id_patron" value="<?= (int)$p['id_patron']; ?>">
                    <input type="hidden" name="nuevo_estado" value="<?= $p['estado_patron'] === 'activo' ? 'archivado' : 'activo'; ?>">
                    <button type="submit" class="btn btn-sm btn-outline-secondary" title="<?= $p['estado_patron'] === 'activo' ? 'Archivar proyecto' : 'Reactivar proyecto'; ?>">
                      <i class="bi <?= $p['estado_patron'] === 'activo' ? 'bi-archive-fill' : 'bi-arrow-counterclockwise'; ?>"></i>
                    </button>
                  </form>

                  <!-- Botón Descargar (si tiene archivo o json) -->
                  <?php if ($tieneArchivo): ?>
                    <a href="/FromIA-HTML/dashboard.php?seccion=proyectos&descargar=<?= (int)$p['id_patron']; ?>" class="btn btn-sm btn-outline-info" title="Descargar archivo / especificación FormAI" download>
                      <i class="bi bi-download"></i>
                    </a>
                  <?php else: ?>
                    <button type="button" class="btn btn-sm btn-outline-secondary" disabled title="Sin archivo adjunto" style="opacity: 0.35; cursor: not-allowed;">
                      <i class="bi bi-download"></i>
                    </button>
                  <?php endif; ?>
                </div>

                <!-- Botón Eliminar -->
                <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar-proyecto" 
                        data-id="<?= (int)$p['id_patron']; ?>"
                        data-nombre="<?= htmlspecialchars($p['nomb_patron'], ENT_QUOTES, 'UTF-8'); ?>"
                        title="Eliminar proyecto">
                  <i class="bi bi-trash3-fill"></i>
                </button>
              </div>
            </div>

          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-12 empty-projects-state py-5" style="grid-column: 1 / -1;">
          <div style="font-size: 3.5rem; color: #433d60;" class="mb-3">
            <i class="bi bi-folder-plus"></i>
          </div>
          <h3 class="text-white fw-bold mb-2">Aún no tienes proyectos registrados</h3>
          <p class="text-muted mb-4">Crea tu primer patrón textil, diseño o modelo 3D para comenzar.</p>
          <button type="button" class="btn-formai-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoProyecto">
            <i class="bi bi-plus-lg me-1"></i> Crear Proyecto
          </button>
        </div>
      <?php endif; ?>
    </div>

    <!-- Mensaje cuando la búsqueda no arroja resultados -->
    <div id="sinResultados" class="text-center py-5 d-none" style="grid-column: 1 / -1;">
      <i class="bi bi-search fs-1 text-muted d-block mb-3"></i>
      <h4 class="text-white">No se encontraron proyectos con ese criterio</h4>
      <p class="text-muted">Prueba buscando con otro término o limpiando los filtros.</p>
    </div>

  </main>
</div>

<!-- MODAL 1: CREAR NUEVO PROYECTO -->
<div class="modal fade" id="modalNuevoProyecto" tabindex="-1" aria-labelledby="modalNuevoProyectoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modal-content-dark">
      <form method="POST" action="/FromIA-HTML/dashboard.php?seccion=proyectos" enctype="multipart/form-data">
        <input type="hidden" name="accion" value="crear_patron">
        
        <div class="modal-header">
          <h5 class="modal-title text-white fw-bold" id="modalNuevoProyectoLabel">
            <i class="bi bi-plus-circle-fill text-primary me-2"></i> Crear Nuevo Proyecto
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <div class="modal-body p-4">
          <div class="mb-3">
            <label for="crearNombre" class="form-label text-light fw-semibold" style="font-size: 0.85rem;">Nombre del Proyecto *</label>
            <input type="text" id="crearNombre" name="nomb_patron" class="form-control formai-input-dark" placeholder="Ej. Chaqueta Aviador 2026" required maxlength="120">
          </div>

          <div class="mb-3">
            <label for="crearTipo" class="form-label text-light fw-semibold" style="font-size: 0.85rem;">Tipo de Proyecto *</label>
            <select id="crearTipo" name="tipo_patron" class="form-select formai-input-dark" required>
              <option value="patronaje" selected>Patronaje (Molde / Trazo)</option>
              <option value="diseño">Diseño Textil</option>
              <option value="3d">Simulación 3D</option>
              <option value="mixto">Mixto</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="crearDesc" class="form-label text-light fw-semibold" style="font-size: 0.85rem;">Descripción o Notas</label>
            <textarea id="crearDesc" name="desc_patron" class="form-control formai-input-dark" rows="3" placeholder="Tallas, especificaciones de tela, holguras o detalles..."></textarea>
          </div>

          <div class="mb-2">
            <label for="crearArchivo" class="form-label text-light fw-semibold" style="font-size: 0.85rem;">
              Archivo adjunto (Opcional: DXF, JSON, PDF o Imagen)
            </label>
            <input type="file" id="crearArchivo" name="archivo_patron" class="form-control formai-input-dark" accept=".dxf,.json,.pdf,.png,.jpg,.jpeg,.svg">
            <div class="form-text" style="color: #8c89a4; font-size: 0.75rem;">Formatos aceptados: DXF, JSON, PDF, SVG, PNG, JPG (máx. 15MB).</div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 20px; font-weight: 500;">Cancelar</button>
          <button type="submit" class="btn-formai-primary" style="padding: 8px 22px;">
            <i class="bi bi-check-lg me-1"></i> Guardar Proyecto
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL 2: EDITAR PROYECTO -->
<div class="modal fade" id="modalEditarProyecto" tabindex="-1" aria-labelledby="modalEditarProyectoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modal-content-dark">
      <form method="POST" action="/FromIA-HTML/dashboard.php?seccion=proyectos">
        <input type="hidden" name="accion" value="editar_patron">
        <input type="hidden" id="editarId" name="id_patron" value="">
        
        <div class="modal-header">
          <h5 class="modal-title text-white fw-bold" id="modalEditarProyectoLabel">
            <i class="bi bi-pencil-square text-info me-2"></i> Editar Proyecto
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <div class="modal-body p-4">
          <div class="mb-3">
            <label for="editarNombre" class="form-label text-light fw-semibold" style="font-size: 0.85rem;">Nombre del Proyecto *</label>
            <input type="text" id="editarNombre" name="nomb_patron" class="form-control formai-input-dark" required maxlength="120">
          </div>

          <div class="row g-3 mb-3">
            <div class="col-6">
              <label for="editarTipo" class="form-label text-light fw-semibold" style="font-size: 0.85rem;">Tipo de Proyecto *</label>
              <select id="editarTipo" name="tipo_patron" class="form-select formai-input-dark" required>
                <option value="patronaje">Patronaje</option>
                <option value="diseño">Diseño</option>
                <option value="3d">3D</option>
                <option value="mixto">Mixto</option>
              </select>
            </div>
            <div class="col-6">
              <label for="editarEstado" class="form-label text-light fw-semibold" style="font-size: 0.85rem;">Estado *</label>
              <select id="editarEstado" name="estado_patron" class="form-select formai-input-dark" required>
                <option value="activo">Activo</option>
                <option value="archivado">Archivado</option>
              </select>
            </div>
          </div>

          <div class="mb-2">
            <label for="editarDesc" class="form-label text-light fw-semibold" style="font-size: 0.85rem;">Descripción</label>
            <textarea id="editarDesc" name="desc_patron" class="form-control formai-input-dark" rows="3"></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 20px; font-weight: 500;">Cancelar</button>
          <button type="submit" class="btn-formai-primary" style="padding: 8px 22px;">
            <i class="bi bi-check2 me-1"></i> Actualizar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- FORM OCULTO PARA ELIMINACIÓN -->
<form id="formEliminarProyecto" method="POST" action="/FromIA-HTML/dashboard.php?seccion=proyectos" style="display: none;">
  <input type="hidden" name="accion" value="eliminar_patron">
  <input type="hidden" id="eliminarId" name="id_patron" value="">
</form>

<!-- SCRIPTS DE INTERACCIÓN CLIENTE -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  const buscador = document.getElementById('buscadorProyectos');
  const pillsFiltro = document.querySelectorAll('.filter-pill');
  const tarjetas = document.querySelectorAll('.tarjeta-proyecto');
  const sinResultados = document.getElementById('sinResultados');

  let filtroActual = 'todos';

  function filtrarTarjetas() {
    const texto = buscador ? buscador.value.toLowerCase().trim() : '';
    let visibles = 0;

    tarjetas.forEach(card => {
      const nombre = card.getAttribute('data-nombre') || '';
      const desc = card.getAttribute('data-desc') || '';
      const tipo = card.getAttribute('data-tipo') || '';
      const estado = card.getAttribute('data-estado') || '';

      const coincideTexto = !texto || nombre.includes(texto) || desc.includes(texto);
      let coincideFiltro = false;

      if (filtroActual === 'todos') {
        coincideFiltro = (estado !== 'archivado'); // Por defecto en 'todos' mostramos activos
      } else if (filtroActual === 'archivado') {
        coincideFiltro = (estado === 'archivado');
      } else {
        coincideFiltro = (tipo === filtroActual && estado !== 'archivado');
      }

      if (coincideTexto && coincideFiltro) {
        card.style.display = 'flex';
        visibles++;
      } else {
        card.style.display = 'none';
      }
    });

    if (sinResultados) {
      if (visibles === 0 && tarjetas.length > 0) {
        sinResultados.classList.remove('d-none');
      } else {
        sinResultados.classList.add('d-none');
      }
    }
  }

  // Evento buscador
  if (buscador) {
    buscador.addEventListener('input', filtrarTarjetas);
  }

  // Evento pestañas de filtro
  pillsFiltro.forEach(pill => {
    pill.addEventListener('click', function() {
      pillsFiltro.forEach(p => p.classList.remove('active'));
      this.classList.add('active');
      filtroActual = this.getAttribute('data-filter') || 'todos';
      filtrarTarjetas();
    });
  });

  // Ejecutar filtro inicial
  filtrarTarjetas();

  // Modal Editar Proyecto
  const modalEditarEl = document.getElementById('modalEditarProyecto');

  document.querySelectorAll('.btn-editar-proyecto').forEach(btn => {
    btn.addEventListener('click', function() {
      const id = this.getAttribute('data-id') || '';
      const nombre = this.getAttribute('data-nombre') || '';
      const desc = this.getAttribute('data-desc') || '';
      const tipo = this.getAttribute('data-tipo') || 'patronaje';
      const estado = this.getAttribute('data-estado') || 'activo';

      const inputId = document.getElementById('editarId');
      const inputNombre = document.getElementById('editarNombre');
      const inputDesc = document.getElementById('editarDesc');
      const inputTipo = document.getElementById('editarTipo');
      const inputEstado = document.getElementById('editarEstado');

      if (inputId) inputId.value = id;
      if (inputNombre) inputNombre.value = nombre;
      if (inputDesc) inputDesc.value = desc;
      if (inputTipo) inputTipo.value = tipo;
      if (inputEstado) inputEstado.value = estado;

      if (typeof bootstrap !== 'undefined' && modalEditarEl) {
        const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEditarEl);
        if (modalInstance) {
          modalInstance.show();
        }
      }
    });
  });

  // Confirmación Eliminar Proyecto
  document.querySelectorAll('.btn-eliminar-proyecto').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      const id = this.getAttribute('data-id');
      const nombre = this.getAttribute('data-nombre') || 'este proyecto';

      const ejecutarEliminacion = function() {
        const eliminarInput = document.getElementById('eliminarId');
        const form = document.getElementById('formEliminarProyecto');
        if (eliminarInput && form) {
          eliminarInput.value = id;
          form.submit();
        }
      };

      if (typeof Swal !== 'undefined') {
        Swal.fire({
          title: '¿Eliminar proyecto?',
          text: `Se eliminará "${nombre}". Podrás archivarlo si prefieres conservarlo.`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#e74c3c',
          cancelButtonColor: '#34495e',
          confirmButtonText: 'Sí, eliminar',
          cancelButtonText: 'Cancelar',
          background: '#0d071c',
          color: '#ffffff'
        }).then((result) => {
          if (result.isConfirmed) {
            ejecutarEliminacion();
          }
        });
      } else {
        if (confirm(`¿Estás seguro de eliminar el proyecto "${nombre}"?`)) {
          ejecutarEliminacion();
        }
      }
    });
  });
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
