import './bootstrap'

// CSS de Bootstrap Icons (para los iconos del modal)
import 'bootstrap-icons/font/bootstrap-icons.css'

// JS de Bootstrap (necesario para Modal)
import 'bootstrap'

// jQuery opcional si lo usas en otros lados
import $ from 'jquery'
window.$ = window.jQuery = $

// ==== Helper global del modal ====
// Lo usarás como: showMessageModal({ title, message, variant, extraHtml })
window.showMessageModal = function ({
  title = 'Aviso',
  message = '',
  extraHtml = '',
  variant = 'primary',     // primary | success | warning | danger | info
  icon = null,             // si quieres forzar uno, ej. 'check-circle'
  closeText = 'Aceptar'
} = {}) {
  const el = document.getElementById('messageModal');
  if (!el) return;

  const titleEl = el.querySelector('#messageTitle');
  const textEl  = el.querySelector('#messageText');
  const extraEl = el.querySelector('#messageExtra');
  const btn     = el.querySelector('#messageCloseBtn');
  const iconEl  = el.querySelector('#messageIcon');

  titleEl.textContent = title;
  textEl.textContent  = message;
  extraEl.innerHTML   = extraHtml || '';

  btn.className = 'btn btn-' + variant;
  btn.innerHTML = '<i class="bi bi-check2"></i> ' + closeText;

  const iconMap = {
    primary: 'info-circle',
    info:    'info-circle',
    success: 'check-circle',
    warning: 'exclamation-circle',
    danger:  'exclamation-triangle'
  };
  const iconName = icon || iconMap[variant] || 'info-circle';
  iconEl.className = `bi bi-${iconName} me-2 fs-4 text-${variant}`;

  const modal = new bootstrap.Modal(el);
  modal.show();
};

// Carga tus módulos de auth
import './auth/register.js'
import './auth/login.js'
