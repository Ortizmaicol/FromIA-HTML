// ==========================================
// USUARIOS QUEMADOS DE PRUEBA
// ==========================================
const usuariosQuemados = [
  { email: "admin@gmail.com", password: "Admin123*", rol: "admin" },
  { email: "usuario@gmail.com", password: "User123*", rol: "cliente" },
  { email: "laura@gmail.com", password: "Laura123*", rol: "cliente" }
];

// Reglas Regex de Validación
const patrones = {
  textoNombre: /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{2,50}$/,
  telefono: /^[0-9]{7,10}$/,
  email: /^[a-zA-Z0-9._%+-]+@(gmail|outlook|hotmail|yahoo|icloud|live)\.(com|net|es|co|org)$/i,
  password: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/
};

const setupFormValidation = (formId) => {
  const form = document.getElementById(formId);
  if (!form) return;

  const inputs = form.querySelectorAll('.form-control');

  // Función para determinar el mensaje exacto según las reglas
  function obtenerMensajeError(input) {
    const valor = input.value.trim();

    if (valor === '' && input.hasAttribute('required')) {
      return 'Este campo es obligatorio.';
    }

    if (formId === 'registerForm') {
      if (input.id === 'regNombre') {
        if (!patrones.textoNombre.test(valor)) {
          return 'El nombre solo debe contener letras (2 a 50 caracteres).';
        }
      } else if (input.id === 'regApellido') {
        if (!patrones.textoNombre.test(valor)) {
          return 'El apellido solo debe contener letras (2 a 50 caracteres).';
        }
      } else if (input.id === 'regTelefono') {
        if (!patrones.telefono.test(valor)) {
          return 'El teléfono debe contener entre 7 y 10 dígitos numéricos.';
        }
      } else if (input.id === 'regEmail') {
        if (!patrones.email.test(valor)) {
          return 'Ingresa un correo válido de un dominio permitido (ej. gmail, outlook, hotmail).';
        }
      } else if (input.id === 'regPassword') {
        if (!patrones.password.test(valor)) {
          return 'La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula y un número.';
        }
      } else if (input.id === 'regConfirmPassword') {
        const pass = document.getElementById('regPassword');
        if (!pass || valor !== pass.value.trim()) {
          return 'Las contraseñas no coinciden.';
        }
      }
    }
    return '';
  }

  // Función para evaluar y marcar visualmente el estado del campo
  function validarCampo(input) {
    const mensaje = obtenerMensajeError(input);
    const feedbackDiv = input.parentElement.querySelector('.invalid-feedback');

    if (mensaje !== '') {
      input.setCustomValidity(mensaje);
      input.classList.remove('is-valid');
      input.classList.add('is-invalid');
      if (feedbackDiv) feedbackDiv.textContent = mensaje;
      return false;
    } else {
      input.setCustomValidity('');
      input.classList.remove('is-invalid');
      if (input.value.trim() !== '') {
        input.classList.add('is-valid');
      }
      if (feedbackDiv) feedbackDiv.textContent = '';
      return true;
    }
  }

  // Asignar listeners para evaluar mientras el usuario interactúa
  inputs.forEach((input) => {
    input.addEventListener('input', () => validarCampo(input));
    input.addEventListener('blur', () => validarCampo(input));
  });

  // Evento SUBMIT
  form.addEventListener('submit', (e) => {
    e.preventDefault();

    let esValido = true;
    let primerInputInvalido = null;

    inputs.forEach((input) => {
      const valido = validarCampo(input);
      if (!valido && !primerInputInvalido) {
        primerInputInvalido = input;
        esValido = false;
      }
    });

    if (!esValido) {
      form.classList.add('was-validated');
      if (primerInputInvalido) {
        primerInputInvalido.focus();
        primerInputInvalido.reportValidity();
      }
      return;
    }

    // MANEJO DE INICIO DE SESIÓN
    if (formId === 'loginForm') {
      const emailInput = document.getElementById('loginEmail');
      const passwordInput = document.getElementById('loginPassword');

      const emailVal = emailInput.value.trim();
      const passVal = passwordInput.value.trim();

      // Búsqueda en el arreglo de usuarios quemados (Opción B)
      const usuarioEncontrado = usuariosQuemados.find(
        u => u.email === emailVal && u.password === passVal
      );

      if (usuarioEncontrado) {
        const tituloMensaje = usuarioEncontrado.rol === 'admin' 
          ? '¡Bienvenido Administrador!' 
          : '¡Bienvenido a FormAI!';

        Swal.fire({
          title: tituloMensaje,
          text: 'Has iniciado sesión con éxito.',
          icon: 'success',
          confirmButtonColor: '#336699',
          background: '#1a1a1a',
          color: '#fff',
          confirmButtonText: 'Ir al Dashboard'
        }).then((result) => {
          if (result.isConfirmed || result.isDismissed) {
            window.location.href = 'dashboard.html';
          }
        });
      } else {
        // Credenciales incorrectas
        emailInput.classList.add('is-invalid');
        passwordInput.classList.add('is-invalid');
        
        const mensajeError = 'El correo o la contraseña son incorrectos.';
        emailInput.setCustomValidity(mensajeError);
        
        const feedbackDiv = emailInput.parentElement.querySelector('.invalid-feedback');
        if (feedbackDiv) feedbackDiv.textContent = mensajeError;

        emailInput.focus();
        emailInput.reportValidity();
      }
    } 
    // MANEJO DE REGISTRO
    else if (formId === 'registerForm') {
      Swal.fire({
        title: '¡Registro Exitoso!',
        text: 'Tu cuenta ha sido creada correctamente.',
        icon: 'success',
        confirmButtonColor: '#336699',
        background: '#1a1a1a',
        color: '#fff',
      });
    }
  });
};

// Inicialización de ambos formularios en login.html
document.addEventListener('DOMContentLoaded', () => {
  setupFormValidation('loginForm');
  setupFormValidation('registerForm');
});