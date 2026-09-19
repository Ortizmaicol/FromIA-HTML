<?php
$pageTitle = "FormAI - IniciarSesion/Registrarse";
$extraCss  = "auth.css";
require_once __DIR__ . '/../layouts/header.php';
?>


<main class="auth-wrapper">
  <h1 class="auth-main-title">IniciarSesion/Registrarse</h1>
  <p class="auth-sub-desc">
    Con el botón de selección puedes elegir si quieres iniciar sesión o crear una nueva cuenta.
  </p>

  <!-- Switch Selector -->
  <div class="switch-container">
    <span id="labelLogin" class="switch-label active">IniciarSesion</span>
    <div class="form-check form-switch custom-switch m-0">
      <input class="form-check-input" type="checkbox" role="switch" id="authToggle" />
    </div>
    <span id="labelRegister" class="switch-label inactive">Registrarse</span>
  </div>

  <!-- Mensajes de feedback del servidor -->
  <?php if (!empty($error)): ?>
    <div class="alert alert-danger w-100 mb-3 text-center" style="max-width: 950px; border-radius: 10px; background-color: rgba(220, 53, 69, 0.2); border-color: #dc3545; color: #ff8080;" role="alert">
      <?= htmlspecialchars($error); ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($success)): ?>
    <div class="alert alert-success w-100 mb-3 text-center" style="max-width: 950px; border-radius: 10px; background-color: rgba(40, 167, 69, 0.2); border-color: #28a745; color: #75e691;" role="alert">
      <?= htmlspecialchars($success); ?>
    </div>
  <?php endif; ?>

  <!-- Tarjeta Central -->
  <div class="card-auth-custom">
    <div class="row g-0">
      
      <div class="col-12 col-lg-7 form-side">
        <h2 class="brand-title">FormAI</h2>
        <div class="brand-sub">FormAI</div>

        <!-- FORMULARIO 1: INICIAR SESIÓN -->
        <form id="loginForm" method="POST" action="/login.php" novalidate>
          <input type="hidden" name="accion" value="login">

          <div class="mb-3">
            <label for="loginEmail" class="custom-label">
              <i class="bi bi-envelope"></i> Correo
            </label>
            <input type="email" name="email" class="form-control custom-input" id="loginEmail" placeholder="FormAI@gmail.com" required />
            <div class="invalid-feedback"></div>
          </div>

          <div class="mb-4">
            <label for="loginPassword" class="custom-label">
              <i class="bi bi-lock"></i> Contraseña
            </label>
            <input type="password" name="password" class="form-control custom-input" id="loginPassword" placeholder="**********" required />
            <div class="invalid-feedback"></div>
          </div>

          <button type="submit" class="btn btn-auth-submit w-100 mt-2">Iniciar Sesión</button>
        </form>

        <!-- FORMULARIO 2: REGISTRARSE -->
        <form id="registerForm" method="POST" action="/login.php" style="display: none;" novalidate>
          <input type="hidden" name="accion" value="registro">

          <div class="row g-2 mb-3">
            <div class="col-6">
              <label for="regNombre" class="custom-label">Nombre</label>
              <input type="text" name="nombre" class="form-control custom-input" id="regNombre" placeholder="Laura" required />
              <div class="invalid-feedback"></div>
            </div>
            <div class="col-6">
              <label for="regApellido" class="custom-label">Apellido</label>
              <input type="text" name="apellido" class="form-control custom-input" id="regApellido" placeholder="Díaz" required />
              <div class="invalid-feedback"></div>
            </div>
          </div>

          <div class="mb-3">
            <label for="regTelefono" class="custom-label">Teléfono</label>
            <input type="tel" name="telefono" class="form-control custom-input" id="regTelefono" placeholder="3001234567" required />
            <div class="invalid-feedback"></div>
          </div>

          <div class="mb-3">
            <label for="regEmail" class="custom-label">Correo Electrónico</label>
            <input type="email" name="email" class="form-control custom-input" id="regEmail" placeholder="usuario@gmail.com" required />
            <div class="invalid-feedback"></div>
          </div>

          <div class="mb-3">
            <label for="regPassword" class="custom-label">Contraseña</label>
            <input type="password" name="password" class="form-control custom-input" id="regPassword" placeholder="Mínimo 8 caracteres" required />
            <div class="invalid-feedback"></div>
          </div>

          <div class="mb-4">
            <label for="regConfirmPassword" class="custom-label">Confirmar Contraseña</label>
            <input type="password" class="form-control custom-input" id="regConfirmPassword" placeholder="Repite tu contraseña" required />
            <div class="invalid-feedback"></div>
          </div>

          <button type="submit" class="btn btn-auth-submit w-100 mt-2">Crear Cuenta</button>
        </form>

      </div>

      <!-- Columna Derecha con Gráfico de Moda -->
      <div class="col-12 col-lg-5 image-side">
        <img src="/Img/IconoFormAIDark.avif" alt="Icono FormAI" />
      </div>

    </div>
  </div>
</main>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const authToggle = document.getElementById('authToggle');
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const labelLogin = document.getElementById('labelLogin');
    const labelRegister = document.getElementById('labelRegister');

    if (authToggle) {
      authToggle.addEventListener('change', function () {
        if (this.checked) {
          loginForm.style.display = 'none';
          registerForm.style.display = 'block';
          labelRegister.className = 'switch-label active';
          labelLogin.className = 'switch-label inactive';
        } else {
          registerForm.style.display = 'none';
          loginForm.style.display = 'block';
          labelLogin.className = 'switch-label active';
          labelRegister.className = 'switch-label inactive';
        }
      });
    }
  });
</script>
<script src="/JS/login.js"></script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>