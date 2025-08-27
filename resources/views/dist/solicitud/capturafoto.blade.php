<!-- Modal Captura Selfie (estilo conservado) -->
<div class="modal fade" id="tomarFotoModal" tabindex="-1" aria-labelledby="tomarFotoModalLabel" aria-hidden="true">
  <form id="formTomarFoto">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">

        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title w-100 text-center" id="tomarFotoModalLabel">Capturar Selfie</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <div class="modal-body">
          <!-- Solo 1 tab visible: Selfie -->
          <div class="tab-content mt-3" id="tabsTomarFotoContent">
            <div class="tab-pane fade show active" id="tab-selfie" role="tabpanel">
              <div class="text-center position-relative">
                <!-- Contenedor Circular del Video (idéntico) -->
                <div id="videoContainer">
                  <video id="videoSelfie" autoplay muted></video>

                  <!-- Loader y Marcadores (idénticos) -->
                  <div id="loaderCircular"></div>

                  <div id="indicadoresDireccion">
                    <i class="bi bi-dot indicador" id="indicador-frente" title="Frente"></i>
                    <i class="bi bi-arrow-up-circle-fill indicador" id="indicador-arriba" title="Arriba"></i>
                    <i class="bi bi-arrow-right-circle-fill indicador" id="indicador-derecha" title="Derecha"></i>
                    <i class="bi bi-arrow-down-circle-fill indicador" id="indicador-abajo" title="Abajo"></i>
                    <i class="bi bi-arrow-left-circle-fill indicador" id="indicador-izquierda" title="Izquierda"></i>
                  </div>
                </div>

                <!-- Instrucción + estado (idénticos) -->
                <div id="instruccionSelfie" class="text-center mt-3 fs-5 fw-bold text-dark">
                  Mira al frente
                </div>

                <div class="d-flex justify-content-center gap-2 mt-2">
                    <button type="button" class="btn btn-primary" id="btnCapturarMovimiento">
                        <i class="bi bi-camera"></i> Tomar foto
                    </button>
                    <button type="button" class="btn btn-outline-secondary d-none" id="btnRepetirSelfie">
                        <i class="bi bi-arrow-counterclockwise"></i> Repetir
                    </button>
                </div>

                <div id="estadoCaptura" class="text-muted text-center mt-2 fs-6"></div>

                <!-- Canvas de previsualización (idéntico) -->
                <canvas id="canvasSelfie" width="320" height="240" class="d-none mt-2 border"></canvas>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer: solo Finalizar -->
        <div class="modal-footer d-flex justify-content-end">
          <button type="button" class="btn btn-success" id="btnFinalizar" data-bs-dismiss="modal">
            Finalizar
          </button>
        </div>

      </div>
    </div>
  </form>
</div>


<style>
    /* Contenedor circular adaptativo del video */
    /* Contenedor circular responsivo del video */
    #videoContainer {
        position: relative;
        width: 100%;
        max-width: 300px;
        margin: 0 auto;
        aspect-ratio: 1 / 1;
        border-radius: 50%;
        overflow: hidden;
        border: 5px solid #fff;
        box-shadow: 0 0 15px rgba(0,0,0,0.3);
    }

    /* Video ocupa todo el contenedor */
    #videoSelfie {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    /* Círculo de íconos alrededor del video */
   #indicadoresDireccion {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 10;
    }

    .indicador {
        position: absolute;
        font-size: 1.8rem;
        color: #ccc;
        transition: color 0.3s ease;
    }

    /* Posicionamiento en forma de reloj */
    #indicador-frente    { top: 5%;   left: 50%; transform: translate(-50%, -50%); }
    #indicador-arriba    { top: 20%;  left: 50%; transform: translate(-50%, -50%); }
    #indicador-derecha   { top: 50%;  left: 90%; transform: translate(-50%, -50%); }
    #indicador-abajo     { top: 80%;  left: 50%; transform: translate(-50%, -50%); }
    #indicador-izquierda { top: 50%;  left: 10%; transform: translate(-50%, -50%); }

    /* Estados */
    .indicador.activo {
        color: #ffc107; /* amarillo */
    }
    .indicador.completado {
        color: #28a745; /* verde */
    }

    /* Responsive menor a 400px */
    @media (max-width: 400px) {
        .indicador {
            width: 28px;
            height: 28px;
            font-size: 1.2rem;
        }
    }

/* Círculo animado de carga inicial (modo frente) */
#loaderCircular {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    border: 5px solid rgba(0,0,0,0.1);
    border-top-color: #0d6efd;
    animation: girar 1s linear infinite;
    z-index: 20;
    display: none;
    box-sizing: border-box;
}

@keyframes girar {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

#indicador-frente {
    display: none;
}

</style>