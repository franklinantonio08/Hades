/**
 * resources/js/app.js
 * App principal: configura Bootstrap, jQuery y define un helper global
 * para mostrar el modal de mensajes en cualquier vista.
 */

// (Opcional) tu bootstrap propio: axios, csrf, etc.
import './bootstrap'

// Bootstrap Icons (para los <i class="bi ...">)
import 'bootstrap-icons/font/bootstrap-icons.css'

// Importa la clase Modal (y Tooltip opcional) correctamente
import { Modal, Tooltip } from 'bootstrap'

// jQuery disponible globalmente si lo necesitas en otros módulos
import $ from 'jquery'
window.$ = window.jQuery = $

/* ============================================
   Helper GLOBAL para abrir el modal de mensajes
   Blade esperado: includes/messagebasicmodal.blade.php
   IDs: #messageBasicModal, #modalTitle, #modalMessage,
        #modalExtraInfoDiv, #modalAlert
   ============================================ */
window.showMessageModal = function ({
  title = 'Aviso',
  message = '',
  extraHtml = '',
  variant = 'info',       // primary | success | warning | danger | info
  icon = null,            // 'check-circle', 'info-circle', etc.
  closeText = 'Aceptar'
} = {}) {
  const el = document.getElementById('messageBasicModal')
  if (!el) return

  // Elementos reales del Blade
  const titleEl = el.querySelector('#modalTitle')             // <span id="modalTitle">
  const msgEl   = el.querySelector('#modalMessage')           // <div id="modalMessage">
  const extraEl = el.querySelector('#modalExtraInfoDiv')      // <div id="modalExtraInfoDiv">
  const alertEl = el.querySelector('#modalAlert')             // <div id="modalAlert" class="alert ...">
  const closeBtn = el.querySelector('.modal-footer .btn')     // Botón "Cerrar"
  const iconEl  = el.querySelector('.modal-title i')          // <i class="bi ..."> dentro del título

  if (titleEl) titleEl.textContent = title
  if (msgEl)   msgEl.textContent   = message
  if (extraEl) extraEl.innerHTML   = extraHtml || ''

  // Cambia clases según variante
  if (alertEl) alertEl.className = `alert alert-${variant} d-flex align-items-center gap-2 mb-3`
  if (closeBtn) {
    closeBtn.className = `btn btn-${variant} px-4`
    // mantiene tu ícono por defecto
    closeBtn.innerHTML = `<i class="bi bi-check2-circle me-1"></i> ${closeText}`
  }

  // Icono en el título según variante (si no se fuerza con `icon`)
  const iconMap = {
    primary: 'info-circle',
    info:    'info-circle',
    success: 'check-circle',
    warning: 'exclamation-circle',
    danger:  'exclamation-triangle'
  }
  const iconName = icon || iconMap[variant] || 'info-circle'
  if (iconEl) iconEl.className = `bi bi-${iconName}`

  // Muestra el modal usando la instancia de Bootstrap correcta
  const modal = Modal.getOrCreateInstance(el)
  modal.show()
}

/* ============================================
   (Opcional) Helper para mostrar SOLO UNA VEZ
   el modal de bienvenida por navegador/vista.
   Úsalo cuando quieras:
   showMessageModalOnce('bienvenida_registro', { ... })
   ============================================ */
window.showMessageModalOnce = function (key, options) {
  try {
    const k = `msg_once_${key}`
    if (localStorage.getItem(k)) return
    window.showMessageModal(options)
    localStorage.setItem(k, '1')
  } catch (_) {
    // Si localStorage falla (modo privado), igual mostramos el modal
    window.showMessageModal(options)
  }
}

/* ============================================
   (Opcional) Activar tooltips globalmente
   ============================================ */
document.addEventListener('DOMContentLoaded', () => {
  try {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.forEach(el => new Tooltip(el))
  } catch (_) {}
})

/* ============================================
   Importa tus módulos de autenticación
   (estos archivos pueden llamar a window.showMessageModal)
   ============================================ */
import './auth/register.js'
import './auth/login.js'

/* ============================================
   (Opcional) Ejemplo de uso global al cargar:
   Descomenta si quieres un mensaje general en todas las vistas
   que incluyan el modal.
   ============================================ */
// document.addEventListener('DOMContentLoaded', () => {
//   showMessageModal({
//     title: 'Bienvenido',
//     message: 'Por favor completa tu registro para continuar.',
//     variant: 'info'
//   })
// })
