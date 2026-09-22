<?php
$pageTitle = "Membresías - FormAI";
$extraCss  = "pricing.css";

require_once __DIR__ . '/../../models/SuscripcionModel.php';
$suscripcionModel = new SuscripcionModel();
$planes = $suscripcionModel->obtenerPlanesDisponibles();

require_once __DIR__ . '/../layouts/header.php';
$isLogged = isset($_SESSION['user_id']);
?>

<main class="container py-5">
  <div class="text-center">
    <span class="pricing-badge-top">Precios y Planes</span>
    <h1 class="pricing-header-title">Membresías FormAI</h1>
    <p class="pricing-header-sub">
      Elige el plan que mejor se adapte a tus necesidades y lleva tus diseños y patronaje textil al siguiente nivel.
    </p>

    <!-- TOGGLE SWITCH (MENSUAL / ANUAL) -->
    <div class="pricing-toggle-container">
      <span class="pricing-toggle-label" id="lblMonthly" style="color: #ffffff;">Mensual</span>
      <div class="form-check form-switch custom-switch m-0">
        <input class="form-check-input" type="checkbox" role="switch" id="pricingToggle" />
      </div>
      <span class="pricing-toggle-label" id="lblAnnually" style="color: #888888;">Anual <span class="badge bg-success ms-1" style="font-size: 0.7rem;">Ahorra 20%</span></span>
    </div>
  </div>

  <div class="row g-4 justify-content-center">
    <?php foreach ($planes as $p): ?>
      <?php
        $idPlan = (int)$p['id_plan'];
        $esPopular = ($idPlan === 2);
        $enlaceBoton = $isLogged ? "/FromIA-HTML/dashboard.php?seccion=membresia&plan={$idPlan}" : "/FromIA-HTML/login.php";
      ?>
      <div class="col-12 col-md-6 col-lg-4">
        <div class="pricing-card <?= $esPopular ? 'popular' : ''; ?>">
          <?php if ($esPopular): ?>
            <span class="popular-tag">Popular</span>
          <?php endif; ?>

          <div class="plan-title">
            <?php if ($idPlan === 1): ?><i class="bi bi-mortarboard"></i>
            <?php elseif ($idPlan === 2): ?><i class="bi bi-lightning-charge"></i>
            <?php else: ?><i class="bi bi-building"></i><?php endif; ?>
            <?= htmlspecialchars($p['nombre_plan']); ?>
          </div>

          <div class="plan-price">
            <span class="currency">$<span class="val-price" data-mensual="<?= number_format((float)$p['precio_mensual'], 2); ?>" data-anual="<?= number_format((float)$p['precio_anual'], 2); ?>"><?= number_format((float)$p['precio_mensual'], 2); ?></span></span>
            <span class="period" id="periodoText">/mes</span>
          </div>

          <p class="plan-description">
            <?php if ($idPlan === 1): ?>
              Perfecto para estudiantes y creadores independientes que inician en el patronaje digital.
            <?php elseif ($idPlan === 2): ?>
              Diseñado para talleres y marcas de moda en crecimiento con exportaciones ilimitadas y 3D.
            <?php else: ?>
              Solución corporativa para fábricas de confección con alto volumen y soporte VIP.
            <?php endif; ?>
          </p>

          <a href="<?= $enlaceBoton; ?>" class="btn btn-plan <?= $esPopular ? 'primary' : ''; ?>">
            <?= $isLogged ? 'Contratar este plan' : 'Iniciar sesión para elegir'; ?>
          </a>

          <div class="features-title">Qué incluye:</div>
          <ul class="feature-list">
            <?php if ($idPlan === 1): ?>
              <li>Hasta 15 proyectos activos en la nube</li>
              <li>Exportación en formato JSON FormAI</li>
              <li>Asistente inteligente de patronaje</li>
              <li>Soporte técnico por correo</li>
            <?php elseif ($idPlan === 2): ?>
              <li>Proyectos y patrones ilimitados</li>
              <li>Exportaciones DXF, PDF, SVG y JSON</li>
              <li>Simulación y visualización 3D</li>
              <li>Gradación automática de tallas</li>
              <li>Soporte prioritario 24/7</li>
            <?php else: ?>
              <li>Todo lo incluido en Profesional</li>
              <li>Integración API para producción</li>
              <li>Múltiples cuentas colaborativas</li>
              <li>Consultoría y capacitación dedicada</li>
              <li>Soporte VIP telefónico y chat</li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</main>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const pricingToggle = document.getElementById('pricingToggle');
    const lblMonthly = document.getElementById('lblMonthly');
    const lblAnnually = document.getElementById('lblAnnually');
    const priceElements = document.querySelectorAll('.val-price');
    const periodElements = document.querySelectorAll('.period');

    if (pricingToggle) {
      pricingToggle.addEventListener('change', function () {
        const esAnual = this.checked;
        if (esAnual) {
          lblAnnually.style.color = '#ffffff';
          lblMonthly.style.color = '#888888';
          periodElements.forEach(el => el.textContent = '/año facturado');
          priceElements.forEach(el => {
            el.textContent = el.getAttribute('data-anual');
          });
        } else {
          lblMonthly.style.color = '#ffffff';
          lblAnnually.style.color = '#888888';
          periodElements.forEach(el => el.textContent = '/mes');
          priceElements.forEach(el => {
            el.textContent = el.getAttribute('data-mensual');
          });
        }
      });
    }
  });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>