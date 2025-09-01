<!-- Modal Mejorado -->
<div class="modal fade" id="solicitudModal" tabindex="-1" aria-labelledby="solicitudModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Header del Modal -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="solicitudModalLabel">Detalles de la Multa</h5>
                <button type="button" class="btn-close btn-secondary btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <!-- Cuerpo del Modal -->
            <div class="modal-body">

                <div id="estado-progress" class="mb-3"></div>

                <div id="modal-content" class="text-start">
                    <p class="text-muted">Cargando datos...</p>
                </div>
            </div>
            
            <!-- Footer del Modal -->
       

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>

        </div>
    </div>
</div>

<!-- Estilos personalizados opcionales -->
<style>
    #solicitudModalLabel {
        font-weight: bold;
    }
    #modal-content p {
        font-size: 1rem;
    }
    .modal-footer button {
        font-size: 0.9rem;
    }
    /* --- Timeline Pro --- */
    .t-card{
    background: var(--bs-light);
    border: 1px solid var(--bs-border-color);
    border-radius: .75rem;
    padding: 1rem;
    box-shadow: 0 .125rem .25rem rgba(0,0,0,.06);
    }

    .tline{ position:relative; margin:.25rem 0 0; padding-top:32px; }
    .tline-bg{
    position:absolute; left:0; right:0; top:14px;
    height:8px; background: var(--bs-gray-200); border-radius:6px;
    }
    .tline-fill{
    position:absolute; left:0; top:14px;
    height:8px; width:0%;
    background: linear-gradient(90deg, #198754 0%, #1f9d68 50%, #198754 100%); /* verde, un solo tono con degradé suave */
    border-radius:6px;
    box-shadow: 0 0 0 1px rgba(0,0,0,.02) inset, 0 .125rem .25rem rgba(25,135,84,.15);
    transition: width .45s cubic-bezier(.22,.61,.36,1);
    }
    .tsteps{
    display:flex; justify-content:space-between; align-items:flex-start;
    position:relative; z-index:2;
    }

    .t-step{ position:relative; text-align:center; flex:1; }
    .t-dot{
    position:absolute; top:5px; left:50%; transform:translateX(-50%);
    width:18px; height:18px; border-radius:50%;
    background: var(--bs-body-bg);
    border:3px solid var(--bs-success);
    transition: all .25s ease-in-out;
    }
    .t-step.current .t-dot{ box-shadow: 0 0 0 .35rem rgba(25,135,84,.12); }
    .t-step.done .t-dot{ background: var(--bs-success); color:#fff; }
    .t-dot i{
    position:absolute; top:50%; left:50%; transform:translate(-50%,-50%);
    font-size:12px; line-height:1; pointer-events:none;
    }

    .t-label{
    display:block; font-size:.8rem; margin-top:24px; line-height:1.15;
    white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
    max-width:120px; margin-left:auto; margin-right:auto;
    color: var(--bs-secondary-color);
    }
    .t-step.done .t-label{ color: var(--bs-success); font-weight:600; }

    @media (max-width:576px){ .t-label{ max-width:80px; font-size:.74rem; } }

</style>

<!-- Scripts adicionales si es necesario -->
