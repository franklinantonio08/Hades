// resources/js/auth/register.js

import $ from 'jquery';

class RegisterHandler {
  constructor() {
    this.$form = $('#registerForm');
    this.$btnSubmit = $('#btnRegister');
    this.csrfToken = $('meta[name="csrf-token"]').attr('content');
  }

  init() {
    console.log('por aqui');

    
    if (!this.$form.length) return;

    // console.log('por aqui');

    this.togglePassword();
    this.capsLockHint();
    this.toggleTipoUsuario();
    this.validateIdoneidad();
    this.setupAjaxSubmit();
    this.setupFiliacionLookup();

    // this.showModal(
    //     'Bienvenido',
    //     'Por favor completa tu registro para continuar.',
    //     'info'
    // );


  }

  togglePassword() {
    const $pwd = $('#password');
    const $toggle = $('#togglePwd');
    const $icon = $('#toggleIcon');

    $toggle.on('click', () => {
      const isText = $pwd.attr('type') === 'text';
      $pwd.attr('type', isText ? 'password' : 'text');
      $icon.toggleClass('bi-eye bi-eye-slash');
      $pwd.focus();
    });
  }

  capsLockHint() {
    const $pwd = $('#password');
    const $hint = $('#capsHint');

    $pwd.on('keyup keydown focus', (e) => {
      const caps = e.originalEvent.getModifierState('CapsLock');
      $hint.toggle(caps);
    }).on('blur', () => $hint.hide());
  }

  toggleTipoUsuario() {
    const $radios = $('input[name="tipo_usuario"]');
    const $solicitante = $('#sectionSolicitante');
    const $abogado = $('#sectionAbogado');

    const camposSolic = ['documento_tipo', 'documento_numero', 'genero', 'fecha_nacimiento'];
    const camposAbog = ['abogado_id', 'idoneidad', 'firma_estudio'];

    $radios.on('change', () => {
      const isAbogado = $('input[name="tipo_usuario"]:checked').val() === 'abogado';
      $solicitante.toggle(!isAbogado);
      $abogado.toggle(isAbogado);

      this.setRequired(camposSolic, !isAbogado);
      this.setRequired(camposAbog, isAbogado);
      this.setDisabled(camposSolic, isAbogado);
      this.setDisabled(camposAbog, !isAbogado);
    }).trigger('change');
  }

  setRequired(ids, required) {
    ids.forEach(id => {
      const el = document.getElementById(id);
      if (!el) return;
      required ? el.setAttribute('required', 'required') : el.removeAttribute('required');
    });
  }

  setDisabled(ids, disabled) {
    ids.forEach(id => {
      const el = document.getElementById(id);
      if (!el) return;
      el.disabled = disabled;
      if (disabled && el.type === 'file') el.value = '';
    });
  }

  validateIdoneidad() {
    const $input = $('#idoneidad');
    const $fb = $('#idoneidadFeedback');
    const max = parseInt($input.data('max') || 5242880); // 5MB

    $input.on('change', () => {
      const file = $input[0].files[0];
      $input.removeClass('is-invalid');
      $fb.text('');

      if (!file) return;

      if (file.size > max) {
        $input.addClass('is-invalid');
        $fb.text('El archivo excede 5MB.');
        $input.val('');
        return;
      }

      if (!['application/pdf', 'image/jpeg', 'image/png'].includes(file.type)) {
        $input.addClass('is-invalid');
        $fb.text('Formato no permitido. Usa PDF/JPG/PNG.');
        $input.val('');
      }
    });
  }

  setupFiliacionLookup() {
    const $btn = $('#btnValidarFiliacion');
    const ENDPOINT_FILIACION = window.ENDPOINT_FILIACION;

    $btn.on('click', async () => {
      const tipo = $('#documento_tipo').val();
      const numero = $('#documento_numero').val();

      if (tipo !== 'Ruex' || !numero) {
        return this.showModal('Validación de filiación', 'Selecciona "N° Filiación" y escribe el número.', 'warning');
      }

      $btn.prop('disabled', true);

      try {
        const res = await fetch(ENDPOINT_FILIACION, {
          method: 'POST',
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': this.csrfToken,
          },
          body: JSON.stringify({ documento_tipo: tipo, documento_numero: numero }),
        });

        const json = await res.json();

        if (res.ok && json?.ok && json?.data) {
          const map = {
            primer_nombre: 'primer_nombre',
            segundo_nombre: 'segundo_nombre',
            primer_apellido: 'primer_apellido',
            segundo_apellido: 'segundo_apellido',
            genero: 'genero',
            fecha_nacimiento: 'fecha_nacimiento'
          };

          for (const k in map) {
            if (json.data[k]) $(`#${map[k]}`).val(json.data[k]);
          }

          this.showModal('Filiación verificada', 'Autocompletamos tus datos desde la base de filiación.', 'success');
        } else {
          this.showModal('Consulta de filiación', 'No se pudo obtener la filiación.', 'danger');
        }
      } catch (e) {
        this.showModal('Error', 'No se pudo conectar con el servidor.', 'danger');
      } finally {
        $btn.prop('disabled', false);
      }
    });
  }

  setupAjaxSubmit() {
    this.$form.on('submit', async (e) => {
      e.preventDefault();

      this.clearFieldErrors();
      this.setLoading(true);

      const formData = new FormData(this.$form[0]);

      try {
        const res = await fetch(this.$form.attr('action'), {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': this.csrfToken,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
          },
          body: formData
        });

        if (res.ok) {
          const redirect = window.ROUTE_VERIFY_NOTICE || '/verify-email';
          this.showModal('Registro exitoso', 'Tu cuenta ha sido creada. Revisa tu correo.', 'success', redirect);
          return;
        }

        if (res.status === 422) {
          const data = await res.json();
          const errors = data?.errors || {};
          Object.keys(errors).forEach(field => this.setFieldError(field, errors[field][0]));
          this.showModal('Revisa los campos', 'Hay errores en el formulario.', 'danger');
          return;
        }

        const text = await res.text();
        console.error('Error', text);
        this.showModal('Error', 'Ocurrió un problema al registrar.', 'danger');

      } catch (err) {
        console.error(err);
        this.showModal('Sin conexión', 'No se pudo contactar al servidor.', 'danger');
      } finally {
        this.setLoading(false);
      }
    });
  }

  clearFieldErrors() {
    this.$form.find('.is-invalid').removeClass('is-invalid');
    this.$form.find('.invalid-feedback[data-js="1"]').remove();
  }

  setFieldError(field, message) {
    const $input = this.$form.find(`[name="${field}"]`);
    if (!$input.length) return;

    $input.addClass('is-invalid');
    const $fb = $('<div class="invalid-feedback" data-js="1"></div>').text(message);
    $input.closest('.form-group, .mb-3, .col-12, .input-group').append($fb);
  }

  setLoading(isLoading) {
    this.$btnSubmit.prop('disabled', isLoading);
    this.$btnSubmit.find('.btn-label').toggleClass('d-none', isLoading);
    this.$btnSubmit.find('.btn-spinner').toggleClass('d-none', !isLoading);
  }

  showModal(title, message, variant = 'info', redirect = null) { 
    window.showMessageModal?.({ title, message, variant });

    const modalEl = document.getElementById('messageBasicModal');
    if (modalEl && redirect) {
      modalEl.addEventListener('hidden.bs.modal', function handler() {
        modalEl.removeEventListener('hidden.bs.modal', handler);
        window.location.assign(redirect);
      });
    }
  }
}

$(document).ready(() => {
  const register = new RegisterHandler();
  register.init();
});
