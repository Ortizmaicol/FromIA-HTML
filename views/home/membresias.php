<?php
$pageTitle = "Membresías - FormAI";
$extraCss  = "pricing.css";
require_once __DIR__ . '/../layouts/header.php';
?>

<main class="container py-5">
  <div class="text-center">
    <span class="pricing-badge-top">Precios</span>
    <h1 class="pricing-header-title">Membresías FormAI</h1>
    <p class="pricing-header-sub">
      Elige el plan que mejor se adapte a tus necesidades y lleva tus diseños textiles al siguiente nivel.
    </p>

    <!-- TOGGLE SWITCH (MENSUAL / ANUAL) -->
    <div class="pricing-toggle-container">
      <span class="pricing-toggle-label" id="lblMonthly" style="color: #ffffff;">Mensual</span>
      <div class="form-check form-switch custom-switch m-0">
        <input class="form-check-input" type="checkbox" role="switch" id="pricingToggle" />
      </div>
      <span class="pricing-toggle-label" id="lblAnnually" style="color: #888888;">Anual</span>
    </div>
  </div>

  <div class="row g-4 justify-content-center">
    <!-- PLAN INICIAL -->
    <div class="col-12 col-md-6 col-lg-4">
      <div class="pricing-card">
        <div class="plan-title">
          <i class="bi bi-rocket-takeoff"></i> Inicial
        </div>
        <div class="plan-price">
          <span class="currency">$<span id="priceStarter">49</span></span>
          <span class="period">/mes</span>
        </div>
        <p class="plan-description">
          Perfecto para pequeñas empresas que inician con la automatización por IA.
        </p>
        <a href="login.php" class="btn btn-plan">Elegir este plan</a>

        <div class="features-title">Qué incluye:</div>
        <ul class="feature-list">
          <li>Automatización básica del flujo de trabajo</li>
          <li>Asistente personal con tecnología</li>
          <li>Análisis e informes estándar</li>
          <li>Soporte por correo y chat</li>
          <li>Hasta 3 integraciones</li>
        </ul>
      </div>
    </div>

    <!-- PLAN PROFESIONAL -->
    <div class="col-12 col-md-6 col-lg-4">
      <div class="pricing-card popular">
        <span class="popular-tag">Popular</span>
        <div class="plan-title">
          <i class="bi bi-lightning-charge"></i> Profesional
        </div>
        <div class="plan-price">
          <span class="currency">$<span id="pricePro">99</span></span>
          <span class="period">/mes</span>
        </div>
        <p class="plan-description">
          Perfecto para empresas en crecimiento que buscan escalar con automatización.
        </p>
        <a href="login.php" class="btn btn-plan primary">Elegir este plan</a>

        <div class="features-title">Qué incluye:</div>
        <ul class="feature-list">
          <li>Automatización avanzada del flujo de trabajo</li>
          <li>Herramientas de ventas y marketing</li>
          <li>Análisis de datos mejorados e información útil</li>
          <li>Soporte al cliente prioritario</li>
          <li>Hasta 10 integraciones</li>
        </ul>
      </div>
    </div>

    <!-- PLAN PERSONALIZADO -->
    <div class="col-12 col-md-6 col-lg-4">
      <div class="pricing-card">
        <div class="plan-title">
          <i class="bi bi-crown"></i> Personalizado
        </div>
        <div class="plan-price" style="font-size: 2.5rem; font-weight: 400; font-family: 'Montserrat', sans-serif;">
          A medida
        </div>
        <p class="plan-description">
          Solución completa para grandes empresas con requerimientos a gran escala.
        </p>
        <a href="404.php" class="btn btn-plan">Agendar una llamada</a>

        <div class="features-title">Qué incluye:</div>
        <ul class="feature-list">
          <li>Automatización personalizable</li>
          <li>Consultor de negocios</li>
          <li>Cumplimiento normativo de nivel empresarial</li>
          <li>Soporte VIP 24/7</li>
          <li>Integraciones de IA ilimitadas</li>
        </ul>
      </div>
    </div>
  </div>
</main>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const pricingToggle = document.getElementById('pricingToggle');
    const priceStarter = document.getElementById('priceStarter');
    const pricePro = document.getElementById('pricePro');
    const lblMonthly = document.getElementById('lblMonthly');
    const lblAnnually = document.getElementById('lblAnnually');

    if (pricingToggle) {
      pricingToggle.addEventListener('change', function () {
        if (this.checked) {
          priceStarter.textContent = '37';
          pricePro.textContent = '75';
          lblAnnually.style.color = '#ffffff';
          lblMonthly.style.color = '#888888';
        } else {
          priceStarter.textContent = '49';
          pricePro.textContent = '99';
          lblMonthly.style.color = '#ffffff';
          lblAnnually.style.color = '#888888';
        }
      });
    }
  });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>