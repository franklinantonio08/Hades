{{-- resources/views/dist/solicitud/instruciones.blade.php --}}

<!-- Modal: Instrucciones -->
<div class="modal fade" id="modalInstrucciones" tabindex="-1" aria-labelledby="modalInstruccionesLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalInstruccionesLabel">
          Actualización de Dirección – Instrucciones
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body p-4">
        <div class="alert alert-info" role="alert">
          <p class="mb-2">
            <strong>Este formulario es para actualizar los datos de dirección de personas migrantes</strong> con estatus de
            <u>Residente Temporal</u> o <u>Residente Permanente</u>.
          </p>
          <p class="mb-0">Tenga a mano sus documentos antes de iniciar.</p>
        </div>

        <div class="mb-4">
          <h6 class="text-primary fw-bold">1) Prueba de domicilio (adjunte <u>una</u> de las siguientes opciones)</h6>
          <ul class="mb-2 ps-3">
            <li><strong>Escritura pública</strong> <em>notariada</em>.</li>
            <li><strong>Contrato de arrendamiento</strong> <em>notariado</em>.</li>
            <li><strong>Carta de responsabilidad</strong> <em>notariada</em>.</li>
            <li><strong>Certificación del Juez de Paz</strong> <em>(no requiere notaría)</em>.</li>
          </ul>
          <small class="text-muted">Elija únicamente una opción. Asegúrese de que el documento esté vigente y legible.</small>
        </div>

        <div class="mb-4">
          <h6 class="text-primary fw-bold">2) Recibo de servicio (adjunte <u>uno</u>)</h6>
          <ul class="mb-2 ps-3">
            <li>Recibo de <strong>luz</strong>, <strong>agua</strong>, <strong>teléfono</strong> o <strong>internet</strong><em> notariado</em>.</li>
          </ul>
          {{-- <p class="mb-2">
            <strong>No requiere notaría</strong> si el recibo está a su nombre.
          </p> --}}
          <div class="alert alert-warning mb-0" role="alert">
            Si el recibo está a nombre de otra persona, deberá <u>notariarlo</u> y adjuntar además la <strong>cédula</strong> de la persona titular del recibo.
          </div>
        </div>

        <div class="mb-4">
          <h6 class="text-primary fw-bold">3) Identificación migratoria</h6>
          <ul class="mb-0 ps-3">
            <li>Fotografía nítida del <strong>carnet vigente</strong> (<em>anverso y reverso</em>).</li>
          </ul>
        </div>

        <div class="mb-4">
          <h6 class="text-primary fw-bold">4) Validación de identidad con fotografía</h6>
          <p class="mb-2">
            El sistema le solicitará tomarse una <strong>foto en el momento</strong> para validar su identidad
            (verificación biométrica). Esta acción es <u>obligatoria</u>.
          </p>
          <ul class="mb-0 ps-3">
            <li>Busque un lugar iluminado y mire de frente a la cámara.</li>
            <li>Evite gorras, lentes oscuros o filtros.</li>
          </ul>
        </div>

        <div class="mb-4">
          <h6 class="text-primary fw-bold">5) Multa (si aplica)</h6>
          <p class="mb-0">
            Si su caso califica para multa, el sistema le indicará el monto de <strong>B/. 100.00</strong> y los pasos a seguir.
          </p>
        </div>

        <div class="mb-4">
          <h6 class="text-primary fw-bold">Recomendaciones para los archivos</h6>
          <ul class="mb-0 ps-3">
            <li>Documentos <em>legibles</em> y completos (sin recortes).</li>
            <li>Formatos permitidos: PDF, JPG o PNG.</li>
            <li>Evite fotografías borrosas o con reflejos.</li>
          </ul>
        </div>

        <div class="alert alert-secondary" role="alert">
          <p class="fw-bold mb-2">Aviso de confidencialidad</p>
          <p class="mb-0">
            La información y documentos que aporte serán utilizados exclusivamente para la verificación y actualización de su
            dirección en los sistemas del Servicio Nacional de Migración, conforme a la normativa vigente de protección y uso de datos.
          </p>
        </div>

        {{-- <div class="d-flex flex-wrap gap-2 my-2">
          <button id="nuevaSolicitudForm" type="button" class="btn btn-primary">
            <i class="fa fa-check me-2"></i>INICIAR APLICACIÓN
          </button>
          <button id="recuperaSolicitudForm" type="button" class="btn btn-secondary">
            <i class="fa fa-undo me-2"></i>RECUPERAR APLICACIÓN
          </button>
        </div> --}}
      </div>

      <div class="modal-footer">
        <div class="form-check me-auto">
          <input class="form-check-input" type="checkbox" value="" id="noMostrarDeNuevo">
          <label class="form-check-label" for="noMostrarDeNuevo">
            No mostrar nuevamente
          </label>
        </div>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Entendido</button>
      </div>
    </div>
  </div>
</div>

{{-- Auto-apertura del modal al cargar la página --}}
<script>
(function () {
  const MODAL_ID = 'modalInstrucciones';
  const STORAGE_KEY = 'solicitud_instrucciones_hide';

  function showModal() {
    const el = document.getElementById(MODAL_ID);
    if (!el || typeof bootstrap === 'undefined' || !bootstrap.Modal) return;
    const modal = new bootstrap.Modal(el);
    modal.show();
  }

  document.addEventListener('DOMContentLoaded', function () {
    try {
      const hide = localStorage.getItem(STORAGE_KEY) === '1';
      if (!hide) showModal();

      const chk = document.getElementById('noMostrarDeNuevo');
      if (chk) {
        chk.addEventListener('change', function () {
          if (this.checked) localStorage.setItem(STORAGE_KEY, '1');
          else localStorage.removeItem(STORAGE_KEY);
        });
      }
    } catch (e) {
      showModal();
    }
  });
})();
</script>
