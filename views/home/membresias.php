<?php
$pageTitle = "Membresías - FormAI";
require_once __DIR__ . '/../layouts/header.php';
?>

<style>
  /* ESTILOS DE LA VISTA DE MEMBRESÍAS */
  body {
    background-color: #000000;
    color: #ffffff;
    padding-bottom: 80px;
  }

  .pricing-badge-top {
    display: inline-block;
    background-color: #121212;
    border: 1px solid #2a2a2a;
    color: #888888;
    font-size: 0.75rem;
    padding: 4px 12px;
    border-radius: 4px;
    font-weight: 400;
    margin-bottom: 1rem;
  }

  .pricing-header-title {
    font-family: "Montserrat", sans-serif;
    font-size: clamp(2.2rem, 4vw, 3.2rem);
    font-weight: 800;
    color: #ffffff;
    letter-spacing: -0.5px;
    margin-bottom: 0.8rem;
  }

  .pricing-header-sub {
    color: #a0a0a0;
    font-size: 1rem;
    font-weight: 400;
    max-width: 580px;
    margin: 0 auto 2rem;
    line-height: 1.5;
  }

  .pricing-toggle-container {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    margin-bottom: 3.5rem;
  }

  .pricing-toggle-label {
    font-size: 0.95rem;
    font-weight: 500;
    transition: color 0.3s ease;
  }

  .custom-switch .form-check-input {
    width: 3.2em;
    height: 1.7em;
    background-color: #333333;
    border-color: #555555;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e");
    cursor: pointer;
  }

  .custom-switch .form-check-input:checked {
    background-color: #6f42c1;
    border-color: #6f42c1;
  }

  .pricing-card {
    background: radial-gradient(circle at bottom right, rgba(60, 20, 90, 0.25) 0%, #050505 70%);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 2.2rem 1.8rem;
    height: 100%;
    display: flex;
    flex-direction: column;
    position: relative;
    box-sizing: border-box;
  }

  .pricing-card.popular {
    background: radial-gradient(ellipse at top, rgba(120, 50, 210, 0.35) 0%, #050505 70%);
    border: 1px solid rgba(140, 80, 230, 0.3);
  }

  .popular-tag {
    position: absolute;
    top: 1.8rem;
    right: 1.8rem;
    background-color: #121212;
    border: 1px solid rgba(255, 255, 255, 0.15);
    color: #ffffff;
    font-size: 0.75rem;
    font-weight: 400;
    padding: 3px 10px;
    border-radius: 6px;
  }

  .plan-title {
    font-size: 1.35rem;
    font-weight: 400;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 1.2rem;
    letter-spacing: -0.3px;
  }

  .plan-price {
    font-size: 2.8rem;
    font-weight: 600;
    color: #ffffff;
    line-height: 1;
    margin-bottom: 1rem;
    display: flex;
    align-items: baseline;
  }

  .plan-price span.currency {
    font-size: 2.8rem;
    font-weight: 600;
  }

  .plan-price span.period {
    font-size: 0.95rem;
    font-weight: 400;
    color: #888888;
    margin-left: 2px;
  }

  .plan-description {
    color: #a0a0a0;
    font-size: 0.88rem;
    font-weight: 400;
    line-height: 1.45;
    margin-bottom: 2rem;
    min-height: 42px;
  }

  .btn-plan {
    width: 100%;
    padding: 10px;
    border-radius: 8px;
    font-size: 0.88rem;
    font-weight: 400;
    border: 1px solid rgba(255, 255, 255, 0.15);
    background-color: #0d0d0d;
    color: #ffffff;
    text-decoration: none;
    text-align: center;
    transition: all 0.25s ease;
    margin-bottom: 2.2rem;
    display: block;
  }

  .btn-plan:hover {
    background-color: #181818;
    border-color: rgba(255, 255, 255, 0.3);
    color: #ffffff;
  }

  .btn-plan.primary {
    background-color: #8b4bf2;
    border: none;
    color: #ffffff;
    font-weight: 500;
  }

  .btn-plan.primary:hover {
    background-color: #7937e3;
  }

  .features-title {
    font-size: 0.85rem;
    font-weight: 400;
    color: #ffffff;
    margin-bottom: 1.2rem;
  }

  .feature-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  .feature-list li {
    font-size: 0.88rem;
    font-weight: 400;
    color: #dddddd;
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .feature-list li::before {
    content: '✓';
    color: #ffffff;
    font-size: 0.95rem;
    font-weight: 600;
  }
</style>

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