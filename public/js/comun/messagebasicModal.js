// 


// public/js/comun/messagebasicModal.js
(() => {
  class MessagebasicModal {
    constructor(modalTitle, modalMessage, opts = {}) {
      this.modalTitle = modalTitle ?? 'Mensaje';
      this.modalMessage = modalMessage ?? '';
      this.opts = opts;

      // IDs EXACTOS como en el Blade: messageBasicModal / modalTitle / modalMessage
      this.modalEl = document.getElementById('messageBasicModal');
      this.titleEl = document.getElementById('modalTitle');
      this.msgEl   = document.getElementById('modalMessage');
      this.extraEl = document.getElementById('modalExtraInfoDiv');

      this.bsModal = null; // instancia de bootstrap.Modal
    }

    init() {
      if (!this.modalEl) {
        console.error('[MessagebasicModal] Falta #messageBasicModal en el DOM. ¿Incluiste @include("includes.messagebasicmodal")?');
        return;
      }

      // Setear contenido
      if (this.titleEl) this.titleEl.textContent = this.modalTitle;

      if (this.msgEl) {
        // Usa textContent por defecto; si quieres HTML, pasa { asHtml: true }
        if (this.opts.asHtml) this.msgEl.innerHTML = String(this.modalMessage);
        else this.msgEl.textContent = String(this.modalMessage);
      }

      // Cambiar tipo de alerta si deseas (info, warning, danger, success, primary)
      if (this.opts.type) {
        const alertEl = document.getElementById('modalAlert');
        if (alertEl) {
          alertEl.className = 'alert d-flex align-items-center gap-2 mb-3 rounded-3 alert-' + this.opts.type;
        }
      }

      // Limpiar extra al cerrar completamente
      this.modalEl.addEventListener('hidden.bs.modal', () => {
        if (this.extraEl) this.extraEl.innerHTML = '';
      }, { once: false });

      // Mostrar modal (Bootstrap 5)
      try {
        this.bsModal = bootstrap.Modal.getOrCreateInstance(this.modalEl);
        this.bsModal.show();
      } catch (e) {
        console.error('[MessagebasicModal] bootstrap.Modal no disponible. ¿Cargaste bootstrap.bundle.min.js?', e);
      }
    }

    hide() {
      if (this.bsModal) this.bsModal.hide();
    }

    setExtraHtml(html) {
      if (this.extraEl) this.extraEl.innerHTML = html || '';
    }
  }

  window.MessagebasicModal = MessagebasicModal;
})();
