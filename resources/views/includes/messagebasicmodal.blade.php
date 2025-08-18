{{-- resources/views/includes/messagebasicmodal.blade.php --}}
<div class="modal fade" id="messageBasicModal" tabindex="-1" aria-labelledby="messageBasicModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title d-flex align-items-center gap-2" id="messageBasicModalLabel">
          <i class="bi bi-info-circle"></i>
          <span id="modalTitle">Mensaje</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body pt-2">
        <div id="modalAlert" class="alert alert-info d-flex align-items-center gap-2 mb-3" role="alert" style="border-radius: 12px;">
          <div class="flex-grow-1" id="modalMessage">...</div>
        </div>

        <div id="modalExtraInfoDiv" class="mt-2"></div>
      </div>

      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal">
          <i class="bi bi-check2-circle me-1"></i> Cerrar
        </button>
      </div>
    </div>
  </div>
</div>

{{-- Helper JS para abrir el modal desde cualquier parte --}}
<script>
  (function () {
    let modalInstance = null;

    /**
     * window.showMessageModal({ title, message, variant, extraHtml })
     *  - title: string (opcional, por defecto "Mensaje")
     *  - message: string (texto principal, admite HTML simple)
     *  - variant: "success" | "danger" | "warning" | "info" (default: "info")
     *  - extraHtml: string con HTML adicional (listas de errores, etc.)
     */
    window.showMessageModal = function ({ title = 'Mensaje', message = '', variant = 'info', extraHtml = '' } = {}) {
      const modalEl   = document.getElementById('messageBasicModal');
      const titleEl   = document.getElementById('modalTitle');
      const alertBox  = document.getElementById('modalAlert');
      const msgEl     = document.getElementById('modalMessage');
      const extraEl   = document.getElementById('modalExtraInfoDiv');

      if (!modalEl || !titleEl || !alertBox || !msgEl || !extraEl) return;

      // Título
      titleEl.textContent = title;

      // Cuerpo del mensaje (permite HTML simple)
      msgEl.innerHTML = message || '';

      // Extra (por ejemplo lista <ul> de validaciones)
      extraEl.innerHTML = extraHtml || '';

      // Variantes de Bootstrap
      alertBox.className = 'alert d-flex align-items-center gap-2 mb-3';
      const cls = (variant === 'success' || variant === 'danger' || variant === 'warning' || variant === 'info')
        ? 'alert-' + variant
        : 'alert-info';
      alertBox.classList.add(cls);

      // Instancia y apertura
      if (!modalInstance) {
        modalInstance = new bootstrap.Modal(modalEl, { backdrop: 'static' });
      }
      modalInstance.show();
    };
  })();
</script>
