<!-- Modal Mejorado -->
<div class="modal fade" id="citasModal" tabindex="-1" aria-labelledby="citasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Header del Modal -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="citasModalLabel">Detalles del Infractor</h5>
                <button type="button" class="btn-close btn-secondary btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <!-- Cuerpo del Modal -->
            <div class="modal-body">
                <div id="modal-content" class="text-start">
                    <p class="text-muted">Cargando datos...</p>
                </div>
            </div>
            
            <!-- Footer del Modal -->
       

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>

            </div>
        </div>
    </div>
</div>

<!-- Estilos personalizados opcionales -->
<style>
    #citasModalLabel {
        font-weight: bold;
    }
    #modal-content p {
        font-size: 1rem;
    }
    .modal-footer button {
        font-size: 0.9rem;
    }
</style>

<!-- Scripts adicionales si es necesario -->
