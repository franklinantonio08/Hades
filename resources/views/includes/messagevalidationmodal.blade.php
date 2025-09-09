{{-- <div class="modal fade" id="modalSolicitudActiva" tabindex="-1" aria-labelledby="modalSolicitudActivaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
        <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="modalSolicitudActivaLabel">
            <i class="bi bi-exclamation-triangle me-2"></i> Solicitud activa
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
            Ya tienes una solicitud activa. ¿Deseas tramitar una solicitud para un <strong>familiar</strong>?
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">No, cerrar</button>
            <button type="button" id="btnTramitarFamiliar" class="btn btn-primary">
            <i class="bi bi-people"></i> Sí, tramitar para un familiar
            </button>
        </div>
        </div>
    </div>
</div> --}}




<!-- Modal: Elegir Tipo de Trámite -->
<div class="modal fade" id="modalElegirTipoTramite" tabindex="-1" aria-labelledby="modalElegirTipoTramiteLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalElegirTipoTramiteLabel">
          <i class="bi bi-list-task me-2"></i>
          <span id="tipoTramiteTitle">Tipo de trámite</span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body">
        <p id="tipoTramiteBody" class="mb-2">Elige cómo deseas continuar con la solicitud.</p>
        <small id="tipoTramiteNotes" class="text-muted d-none"></small>
      </div>

      <div class="modal-footer flex-wrap gap-2">
        <!-- Estos dos NO deben mostrarse si el usuario es abogado -->
        <button type="button" id="btnParaMi" class="btn btn-outline-primary">
          <i class="bi bi-person-badge"></i> <span>Tramitar para mí</span>
        </button>
        <button type="button" id="btnParaFamiliar" class="btn btn-primary">
          <i class="bi bi-people"></i> <span>Tramitar para un familiar</span>
        </button>

        <!-- Este SÍ para abogado y también puede aparecer para solicitante si quieres -->
        <button type="button" id="btnParaRepresentado" class="btn btn-success">
          <i class="bi bi-person-check"></i> <span>Tramitar para un representado</span>
        </button>

        <button type="button" class="btn btn-outline-secondary ms-auto" data-bs-dismiss="modal" id="btnCancelar">
          Cancelar
        </button>
      </div>
    </div>
  </div>
</div>
