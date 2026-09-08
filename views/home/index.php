<?php
$pageTitle = "FormAI - Soluciones de Creación de Patrones";
require_once __DIR__ . '/../layouts/header.php';
?>

<style>
  /* ESTILOS EXACTOS DE LAS SECCIONES Y MOCKUPS */
  .feature-section {
    padding: 60px 20px 100px;
    max-width: 1200px;
    margin: 0 auto;
  }

  .main-heading-container {
    text-align: center;
    max-width: 850px;
    margin: 40px auto 80px;
  }

  .main-heading-container h2 {
    font-family: "Montserrat", sans-serif;
    font-size: clamp(2rem, 4.5vw, 3.2rem);
    font-weight: 800;
    line-height: 1.2;
    color: #ffffff;
    margin-bottom: 20px;
    letter-spacing: -0.5px;
  }

  .main-heading-container p {
    color: #a0a0a0;
    font-size: clamp(1rem, 2vw, 1.2rem);
    line-height: 1.6;
  }

  .feature-row {
    display: flex;
    align-items: center;
    gap: 50px;
    margin-bottom: 100px;
    flex-wrap: wrap;
  }

  .feature-row.reverse {
    flex-direction: row-reverse;
  }

  .feature-info {
    flex: 1;
    min-width: 300px;
  }

  .feature-tag {
    display: inline-block;
    background-color: #1a1a1a;
    color: #ffffff;
    font-size: 0.75rem;
    padding: 6px 14px;
    border-radius: 4px;
    border: 1px solid #333;
    margin-bottom: 18px;
    font-weight: 600;
  }

  .feature-info h3 {
    font-family: "Montserrat", sans-serif;
    font-size: clamp(1.8rem, 3vw, 2.5rem);
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 18px;
  }

  .feature-info p {
    color: #aaaaaa;
    font-size: 1rem;
    line-height: 1.6;
    margin-bottom: 15px;
  }

  .feature-badges {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 25px;
  }

  .mini-badge {
    background-color: #161616;
    border: 1px solid #2a2a2a;
    color: #ffffff;
    font-size: 0.82rem;
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: 600;
  }

  .mockup-container {
    flex: 1;
    min-width: 320px;
    background-color: #111111;
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.8);
  }

  /* Mockup 1 */
  .mockup-item {
    background-color: #1c1c1c;
    border: 1px solid #2e2e2e;
    border-radius: 8px;
    padding: 14px 18px;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .mockup-item:last-child { margin-bottom: 0; }
  .mockup-item .item-title { font-weight: 600; font-size: 0.95rem; color: #fff; }
  .mockup-item .item-sub { font-size: 0.75rem; color: #888; }
  .status-text { font-size: 0.85rem; color: #ffffff; font-weight: 500; }

  /* Mockup 2 */
  .modal-mockup {
    background-color: #111111;
  }
  .radio-group {
    display: flex;
    gap: 12px;
    margin-bottom: 16px;
  }
  .radio-box {
    flex: 1;
    background: #181818;
    border: 1px solid #336699;
    padding: 12px 14px;
    border-radius: 6px;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 8px;
    color: #ffffff;
  }
  .radio-box.inactive {
    border-color: #2e2e2e;
    color: #a0a0a0;
  }
  .dropzone {
    border: 1.5px dashed #444;
    border-radius: 6px;
    padding: 30px 20px;
    text-align: center;
    color: #888;
    font-size: 0.85rem;
    margin-bottom: 18px;
  }

  /* Mockup 3 */
  .settings-mockup {
    display: flex;
    gap: 20px;
    background: #111111;
  }
  .lang-list { 
    flex: 1; 
    border-right: 1px solid #2a2a2a; 
    padding-right: 15px; 
    display: flex;
    flex-direction: column;
    gap: 6px;
  }
  .lang-item { 
    padding: 10px 14px; 
    font-size: 0.88rem; 
    color: #888; 
    border-radius: 6px; 
  }
  .lang-item.active { 
    background: #336699; 
    color: #fff; 
    font-weight: 600; 
  }
  .setting-panel { 
    flex: 1.2; 
    display: flex; 
    flex-direction: column; 
    justify-content: center; 
  }

  @media (max-width: 768px) {
    .feature-row, .feature-row.reverse {
      flex-direction: column;
      gap: 30px;
    }
  }
</style>

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
    <img class="content-icon" src="/FromIA/Img/IconoFormAIDark.avif" alt="IconoFormAI">
    <h1>Form-AI</h1>
    <p class="tagline">Donde la moda y la tecnología se unen en cada puntada.</p>
    <div class="buttons">
      <a href="/FromIA/membresias.php" class="btn btn-primary">Membresias ↗</a>
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