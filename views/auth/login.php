<?php
$pageTitle = "FormAI - IniciarSesion/Registrarse";
require_once __DIR__ . '/../layouts/header.php';
?>

<style>
  body {
    background-color: #000000;
    color: #ffffff;
    font-family: 'Montserrat', sans-serif;
    margin: 0;
  }

  .auth-wrapper {
    min-height: calc(100vh - 70px);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px 15px;
    box-sizing: border-box;
  }

  .auth-main-title {
    font-size: clamp(2.2rem, 4vw, 3.2rem);
    font-weight: 800;
    color: #ffffff;
    margin-bottom: 12px;
    letter-spacing: -0.5px;
    text-align: center;
  }

  .auth-sub-desc {
    color: #888888;
    font-size: 0.95rem;
    max-width: 520px;
    text-align: center;
    line-height: 1.5;
    margin: 0 auto 24px;
  }

  /* Selector Toggle */
  .switch-container {
    display: inline-flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 30px;
  }

  .switch-label {
    font-size: 0.9rem;
    font-weight: 600;
    transition: color 0.3s ease;
  }

  .switch-label.active {
    color: #ffffff;
  }

  .switch-label.inactive {
    color: #666666;
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
    background-color: #336699;
    border-color: #336699;
  }

  /* Tarjeta Contenedora */
  .card-auth-custom {
    width: 100%;
    max-width: 950px;
    background-color: #0f1015;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.8);
  }

  .form-side {
    padding: 40px 45px;
  }

  .brand-title {
    font-size: 1.6rem;
    font-weight: 800;
    color: #ffffff;
    margin: 0;
  }

  .brand-sub {
    font-size: 0.8rem;
    color: #666666;
    margin-bottom: 25px;
  }

  /* Estilos de Inputs */
  .custom-label {
    font-size: 0.82rem;
    color: #888888;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .custom-input {
    background-color: #17181f !important;
    border: 1px solid #23242e !important;
    border-radius: 8px !important;
    color: #ffffff !important;
    font-size: 0.88rem;
    padding: 10px 14px;
    transition: border-color 0.2s ease;
  }

  .custom-input:focus {
    border-color: #336699 !important;
    box-shadow: none !important;
  }

  .custom-input::placeholder {
    color: #4a4d5a;
  }

  /* Botón de Envío */
  .btn-auth-submit {
    background-color: #336699;
    color: #ffffff;
    font-size: 0.95rem;
    font-weight: 600;
    padding: 11px;
    border-radius: 20px;
    border: none;
    transition: background-color 0.25s ease;
  }

  .btn-auth-submit:hover {
    background-color: #264d73;
    color: #ffffff;
  }

  /* Lado de Imagen / Gráfico */
  .image-side {
    background: radial-gradient(circle at center, rgba(30, 20, 60, 0.5) 0%, #080314 100%);
    border-left: 1px solid rgba(255, 255, 255, 0.05);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px;
  }

  .image-side img {
    max-width: 75%;
    height: auto;
    filter: drop-shadow(0 0 20px rgba(135, 85, 194, 0.25));
  }

  @media (max-width: 991px) {
    .image-side {
      display: none !important;
    }
    .form-side {
      padding: 30px 25px;
    }
  }
</style>

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
        <form id="loginForm" method="POST" action="/FromIA/login.php" novalidate>
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
        <form id="registerForm" method="POST" action="/FromIA/login.php" style="display: none;" novalidate>
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
        <img src="/FromIA/Img/IconoFormAIDark.avif" alt="Icono FormAI" />
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
<script src="/FromIA/JS/login.js"></script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>