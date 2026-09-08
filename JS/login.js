const patrones = {
  textoNombre: /^[a-zA-ZÃ¡Ã©Ã­Ã³ÃºÃÃ‰ÃÃ“ÃšÃ±Ã‘\s]{2,50}$/,
  telefono: /^[0-9]{7,10}$/,
  email: /^[a-zA-Z0-9._%+-]+@(gmail|outlook|hotmail|yahoo|icloud|live)\.(com|net|es|co|org)$/i,
  password: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/
};

const setupFormValidation = (formId) => {
  const form = document.getElementById(formId);
  if (!form) return;

  const inputs = form.querySelectorAll('.form-control');

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
          return 'El telÃ©fono debe contener entre 7 y 10 dÃ­gitos numÃ©ricos.';
        }
      } else if (input.id === 'regEmail') {
        if (!patrones.email.test(valor)) {
          return 'Ingresa un correo de un dominio vÃ¡lido (gmail, outlook, hotmail, etc.).';
        }
      } else if (input.id === 'regPassword') {
        if (!patrones.password.test(valor)) {
          return 'MÃ­nimo 8 caracteres, una mayÃºscula, una minÃºscula y un nÃºmero.';
        }
      } else if (input.id === 'regConfirmPassword') {
        const pass = document.getElementById('regPassword');
        if (!pass || valor !== pass.value.trim()) {
          return 'Las contraseÃ±as no coinciden.';
        }
      }
    }
    return '';
  }

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

  inputs.forEach((input) => {
    input.addEventListener('input', () => validarCampo(input));
    input.addEventListener('blur', () => validarCampo(input));
  });

  form.addEventListener('submit', (e) => {
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
      e.preventDefault();
      form.classList.add('was-validated');
      if (primerInputInvalido) {
        primerInputInvalido.focus();
        primerInputInvalido.reportValidity();
      }
    }
  });
};

document.addEventListener('DOMContentLoaded', () => {
  setupFormValidation('loginForm');
  setupFormValidation('registerForm');
});
