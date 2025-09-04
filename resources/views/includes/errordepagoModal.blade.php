<!-- Modal de error de pago -->
<div class="modal fade" id="paymentErrorModal" tabindex="-1" aria-labelledby="paymentErrorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="paymentErrorModalLabel">
            <i class="fas fa-exclamation-triangle me-2"></i> Transacción rechazada
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <p class="mb-2" id="pem-message">Lo sentimos, no pudimos procesar tu pago.</p>
          <div class="small text-muted" id="pem-details"></div>
          <hr>
          <ul class="list-unstyled small mb-0" id="pem-meta">
            <!-- Se rellenará dinámicamente (código, resultado, transacción, etc.) -->
          </ul>
        </div>
        <div class="modal-footer">
          <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button class="btn btn-primary" id="pem-try-again" data-bs-dismiss="modal">Intentar de nuevo</button>
        </div>
      </div>
    </div>
  </div>
  