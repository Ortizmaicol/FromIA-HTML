<?php
$pageTitle = "FormAI - Soluciones de Creación de Patrones";
$extraCss  = "home.css";
require_once __DIR__ . '/../layouts/header.php';
?>

<!-- SECCIÓN BIENVENIDA / HERO -->
<section class="Bienvenida">
  <div class="halo">
    <div class="circle-top"></div>
    <div class="circle-bottom"></div>
    <div class="separator-ring"></div>
    <div class="circle-left"></div>
    <div class="circle-right"></div>
    <div class="center-mask"></div>
  </div>

  <div class="energy-particles">
    <div class="particle" style="--angle:0deg;   --delay:0s;   --dur:3.2s"></div>
    <div class="particle" style="--angle:45deg;  --delay:0.6s; --dur:3.6s"></div>
    <div class="particle" style="--angle:90deg;  --delay:1.2s; --dur:3s"></div>
    <div class="particle" style="--angle:135deg; --delay:0.3s; --dur:3.8s"></div>
    <div class="particle" style="--angle:180deg; --delay:0.9s; --dur:3.4s"></div>
    <div class="particle" style="--angle:225deg; --delay:1.5s; --dur:3.1s"></div>
    <div class="particle" style="--angle:270deg; --delay:0.1s; --dur:3.7s"></div>
    <div class="particle" style="--angle:315deg; --delay:1.1s; --dur:3.3s"></div>
  </div>

  <div class="content">
    <img class="content-icon" src="/FromIA-HTML/Img/IconoFormAIDark.avif" alt="IconoFormAI">
    <h1>Form-AI</h1>
    <p class="tagline">Donde la moda y la tecnología se unen en cada puntada.</p>
    <div class="buttons">
      <a href="/FromIA-HTML/membresias.php" class="btn btn-primary">Membresias ↗</a>
      <a href="#" class="btn btn-secondary">Descargas</a>
    </div>
  </div>
</section>

<!-- SECCIÓN CARACTERÍSTICAS -->
<section class="feature-section">

  <div class="main-heading-container">
    <h2>Soluciones de creación de patrones que llevan tu negocio al siguiente nivel.</h2>
    <p>Diseñamos, desarrollamos e implementamos herramientas de automatización que te ayudan a trabajar de forma más inteligente, no más ardua.</p>
  </div>

  <!-- FILA 1: GESTIÓN DE PROYECTOS -->
  <div class="feature-row">
    <div class="mockup-container">
      <div style="font-size: 0.75rem; color: #888; margin-bottom: 14px; font-weight: 600; letter-spacing: 0.5px;">PROYECTOS RECIENTES</div>
      
      <div class="mockup-item">
        <div>
          <div class="item-title"><i class="bi bi-folder-fill me-2 text-white"></i>Proyecto Masculino Formal</div>
          <div class="item-sub">MASCULINO_FORMAL · 2026-04-24</div>
        </div>
        <span class="status-text">Modificado</span>
      </div>

      <div class="mockup-item">
        <div>
          <div class="item-title"><i class="bi bi-folder-fill me-2 text-white"></i>Proyecto Infantil</div>
          <div class="item-sub">INFANTIL · 2026-04-24</div>
        </div>
        <span class="status-text">En Curso</span>
      </div>

      <div class="mockup-item">
        <div>
          <div class="item-title"><i class="bi bi-folder-fill me-2 text-white"></i>Proyecto Femenino Formal</div>
          <div class="item-sub">FEMENINO_FORMAL · 2026-04-24</div>
        </div>
        <span class="status-text">Sincronizado</span>
      </div>
    </div>

    <div class="feature-info">
      <span class="feature-tag">Flujo de trabajo</span>
      <h3>Gestion de Proyectos</h3>
      <p>Implementamos proyectos con modos offline y online para que nunca dejes de crear y diseñar!</p>
      <p>Si ya tienes proyectos creados, no te preocupes, los transferiremos desde la nube para que, independientemente del dispositivo, tus proyectos siempre aparezcan en tu cuenta.</p>
      <div class="feature-badges">
        <span class="mini-badge">Modos offline y online</span>
        <span class="mini-badge">+100 Gestiones Eficientes</span>
      </div>
    </div>
  </div>

  <!-- FILA 2: CREACIÓN E IMPORTACIÓN -->
  <div class="feature-row reverse">
    <div class="mockup-container">
      <div class="modal-mockup">
        <div style="font-weight: 700; margin-bottom: 14px; font-size: 0.95rem;">Importación de Proyecto</div>
        <div class="radio-group">
          <div class="radio-box">
            <i class="bi bi-check-circle-fill text-white"></i> Proyecto Femenino
          </div>
          <div class="radio-box inactive">
            <i class="bi bi-circle text-muted"></i> Ropa Interior
          </div>
        </div>
        <div class="dropzone">
          <i class="bi bi-cloud-arrow-up fs-4 d-block mb-1"></i>
          Arrastra aquí tu archivo .dxf o selecciona de tu equipo
        </div>
        <div class="d-flex justify-content-end gap-2">
          <button class="btn btn-secondary btn-sm" style="font-size: 0.8rem; border-radius: 20px; padding: 6px 16px;">Cancelar</button>
          <button class="btn btn-primary btn-sm" style="font-size: 0.8rem; border-radius: 20px; padding: 6px 16px; background-color: #336699;">Importar</button>
        </div>
      </div>
    </div>

    <div class="feature-info">
      <span class="feature-tag">Creacion de trabajo</span>
      <h3>Creacion e importacion</h3>
      <p>Si alguien te comparte un proyecto, no te preocupes: puedes importarlo fácilmente desde la aplicación. Solo selecciona el archivo, asígnale un nombre y listo. Podrás acceder a él en cualquier momento, y quedará guardado de forma segura en la nube.</p>
      <div class="feature-badges">
        <span class="mini-badge">Dirige</span>
        <span class="mini-badge">Crea</span>
        <span class="mini-badge">Comparte</span>
      </div>
    </div>
  </div>

  <!-- FILA 3: MULTILENGUAJE -->
  <div class="feature-row">
    <div class="mockup-container">
      <div class="settings-mockup">
        <div class="lang-list">
          <div class="lang-item">English</div>
          <div class="lang-item active">Español</div>
          <div class="lang-item">Français</div>
          <div class="lang-item">Deutsch</div>
        </div>
        <div class="setting-panel">
          <div style="font-size: 0.75rem; color: #888; font-weight: 700; letter-spacing: 0.5px;">CONFIGURACIÓN</div>
          <div class="mt-2" style="font-size: 0.95rem; font-weight: 600;">
            Modo Oscuro <span style="font-weight: 400; color: #aaa;">Activado</span>
          </div>
        </div>
      </div>
    </div>

    <div class="feature-info">
      <span class="feature-tag">Personalizacion</span>
      <h3>Multilenguaje</h3>
      <p>FormAI es una aplicación multilingüe diseñada para que puedas usarla sin barreras de idioma, garantizando una experiencia clara y fácil de entender en todo momento!</p>
      <div class="feature-badges">
        <span class="mini-badge">Temas</span>
        <span class="mini-badge">Modelos</span>
        <span class="mini-badge">Perfil</span>
      </div>
    </div>
  </div>

</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>