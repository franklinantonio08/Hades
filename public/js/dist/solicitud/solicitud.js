class Distsolicitud {

    constructor() {

        this.stream = null;
        this.capturas = [];

        this.instrucciones = [
            { id: 'frente',    texto: 'Mira al frente',       icon: 'indicador-frente' },
            { id: 'izquierda', texto: 'Mira a la izquierda',  icon: 'indicador-izquierda' },
            { id: 'derecha',   texto: 'Mira a la derecha',    icon: 'indicador-derecha' },
            { id: 'arriba',    texto: 'Mira hacia arriba',    icon: 'indicador-arriba' },
            { id: 'abajo',     texto: 'Mira hacia abajo',     icon: 'indicador-abajo' }
        ];

        this.estatusColors = {
            'Recibida': 'bg-secondary bg-gradient',
            'En revisión':  'bg-info bg-gradient',
            'Observada': 'bg-warning bg-gradient',
            'Aprobada - con pago':  'bg-success bg-gradient',
            'Aprobada - sin pago':  'bg-success bg-gradient',
            'Multa emitida':    'bg-primary bg-gradient',
            'Rechazada':   'bg-danger bg-gradient',
            'Cancelada':   'bg-dark bg-gradient',
            'default':   'bg-light bg-gradient text-dark'
        };

    }


    init(){
        
        if($('#editarregistro').length) {
            this.cambia_motivo();
        }

        if($('#nuevoregistro').length) {
            
            this.validatesolicitud();
            this.toggleViviendaFields();
        }

        if($('#solicitud').length) {
            this.solicitud();
        }

    
        this.acciones();

        const $modal = $('#tomarFotoModal');
        $modal.on('shown.bs.modal', () => this.iniciarCamara());
        $modal.on('hidden.bs.modal', () => this.detenerCamara());

        // Botones del modal
        $(document)
          .off('click', '#btnCapturarMovimiento')
          .on('click', '#btnCapturarMovimiento', () => this.runSelfieFlow());

        $(document)
          .off('click', '#btnRepetirSelfie')
          .on('click', '#btnRepetirSelfie', () => this.resetSelfieUI(true));

        $(document)
          .off('click', '#btnFinalizar')
          .on('click', '#btnFinalizar', () => this.finalizarSelfieYEnviar());

    }

    acciones(){

        const _this = this;
                
        $("#searchButton").off('click').click(function() {
            _this.solicitud($("#search").val());
        });   

        $('#search').keypress(function(event){
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if(keycode == '13'){
                _this.solicitud($("#search").val());
                event.preventDefault();
                return false;
            }
            event.stopPropagation();
        });

        $('#tv_casa').on('change', function() { 
              _this.toggleViviendaFields();
        });

        $('#tv_edificio').on('change', function() { 
              _this.toggleViviendaFields();
        });

        $('#tv_hotel').on('change', function() { 
              _this.toggleViviendaFields();
        });


        $('#recibo_tercero').on('change', function() {
            _this.toggleReciboFields();
        });    
        
        $('#recibo_mio').on('change', function() {
            _this.toggleReciboFields();
        });


        $('#dom_escritura').on('change', function() {
            _this.toggleDocsByDomicilio();
        });

        $('#dom_arrendamiento').on('change', function() {
            _this.toggleDocsByDomicilio();
        });
        
        $('#dom_responsabilidad').on('change', function() {
            _this.toggleDocsByDomicilio();
        });

        $('#dom_juezpaz').on('change', function() {
            _this.toggleDocsByDomicilio();
        });

        $('#dom_reservahotel').on('change', function() {
            _this.toggleDocsByDomicilio();
        });

        $("#provincia").on("change", function () {
            _this.buscaDistrito();
        });

        $("#distrito").on("change", function () {
            _this.buscaCorregimiento();
        });
        

        $('#guardarForm').off('click').on('click', function() {
            _this.preSubmitCheck();
        });
    
    }

    getBadgeEstatus(estatus) {
        const badgeClass = this.estatusColors[estatus] || this.estatusColors['default'];
        return `<div class="${badgeClass} text-white text-center p-2" style="border-radius:4px; width:100%;">${estatus}</div>`;
    }

    getUserMediaCompat = function (constraints) {
        const nav = navigator;
        // Moderno
        if (nav.mediaDevices && typeof nav.mediaDevices.getUserMedia === 'function') {
            return nav.mediaDevices.getUserMedia(constraints);
        }
        // Legacy (Chrome/Safari viejos/Firefox viejos)
        const legacy = nav.getUserMedia || nav.webkitGetUserMedia || nav.mozGetUserMedia || nav.msGetUserMedia;
        if (legacy) {
            return new Promise((resolve, reject) => legacy.call(nav, constraints, resolve, reject));
        }
        return Promise.reject(new Error('getUserMedia no soportado en este navegador/origen.'));
    };


    async iniciarCamara() {
        const _this = this


        const video = document.getElementById('videoSelfie');
        if (!video) return;

        // Cerrar stream previo
        if (this.stream) _this.detenerCamara();

        // Verifica origen seguro: HTTPS o localhost
        const isSecure = window.isSecureContext || location.protocol === 'https:' || location.hostname === 'localhost';
        if (!isSecure) {
            console.error('getUserMedia requiere HTTPS o localhost.');
            alert('Para usar la cámara, abre esta página en HTTPS o desde localhost.');
            return;
        }

        try {
            // Intenta frontal; si falla, genérico
            try {
            this.stream = await _this.getUserMediaCompat({ video: { facingMode: 'user' }, audio: false });
            } catch (e1) {
            this.stream = await _this.getUserMediaCompat({ video: true, audio: false });
            }

            video.srcObject = this.stream;
            await video.play();
        } catch (err) {
            console.error(err);
            alert('No se pudo acceder a la cámara: ' + (err && err.message ? err.message : err));
        }
    }

    detenerCamara() {
            if (this.stream) {
                this.stream.getTracks().forEach(t => t.stop());
                this.stream = null;
            }
            const video = document.getElementById('videoSelfie');
            if (video) video.srcObject = null;
    }

    /* ====== UTILIDADES ====== */
    delay(ms){ return new Promise(r => setTimeout(r, ms)); }

    async cuentaRegresiva(el) {
        const _this = this
        if (!el) return;
        el.innerText = "Capturando en 3...";
        await _this.delay(800);
        el.innerText = "Capturando en 2...";
        await _this.delay(800);
        el.innerText = "Capturando en 1...";
        await _this.delay(800);
    }

    timestampYYYYMMDD_HHMMSS() {
        const d = new Date();
        const pad = n => String(n).padStart(2, '0');
        return `${d.getFullYear()}${pad(d.getMonth()+1)}${pad(d.getDate())}_${pad(d.getHours())}${pad(d.getMinutes())}${pad(d.getSeconds())}`;
    }

    /* ====== CAPTURA FOTO -> AL FINAL ENVÍA FORMULARIO ====== */
    resetSelfieUI(clearCapture){

        const _this = this;
        const canvas = document.getElementById('canvasSelfie');
        const estado = document.getElementById('estadoCaptura');
        const instruccionDiv = document.getElementById('instruccionSelfie');
        const loader = document.getElementById('loaderCircular');

        // Limpiar TODOS los indicadores
        _this.instrucciones.forEach(s => {
            const el = document.getElementById(s.icon);
            el?.classList.remove('completado','activo');
        });

        // textos + loader
        if (estado) estado.innerText = '';
        if (instruccionDiv) instruccionDiv.innerHTML = `<span class="text-primary fw-bold">Por favor, mantente mirando al frente</span>`;
        if (loader) loader.style.display = 'none';

        // canvas
        if (canvas) {
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            canvas.classList.add('d-none');
        }

        // botones
        $('#btnRepetirSelfie').addClass('d-none');
        $('#btnCapturarMovimiento').prop('disabled', false);

        // capturas
        if (clearCapture) this.capturas = [];
    }

    async runSelfieFlow(){
        
        const _this = this

        const video = document.getElementById('videoSelfie');
        const canvas = document.getElementById('canvasSelfie');
        const estado = document.getElementById('estadoCaptura');
        const instruccionDiv = document.getElementById('instruccionSelfie');
        const loader = document.getElementById('loaderCircular');
        const btn = document.getElementById('btnCapturarMovimiento');

        if (!video || !canvas) return;
        if (!this.stream) await _this.iniciarCamara();

        // UI inicial
        btn.disabled = true;
        $('#btnRepetirSelfie').addClass('d-none');
        this.instrucciones.forEach(s => {
            const el = document.getElementById(s.icon);
            el?.classList.remove('completado','activo');
        });
        canvas.classList.add('d-none');
        if (estado) estado.innerText = '';
        if (instruccionDiv) instruccionDiv.innerHTML = `<span class="text-primary fw-bold">Por favor, mantente mirando al frente</span>`;

        // limpiamos capturas: guardaremos SOLO el último set
        this.capturas = [];

        // helper para capturar un paso
        const capturarPaso = async (step) => {
            // loader + escaneo (solo frente puede ser más largo; aquí uso mismo)
            if (loader) loader.style.display = 'block';
            if (estado) estado.innerText = "Escaneando rostro...";
            await this.delay(1000);
            if (loader) loader.style.display = 'none';

            // indicador activo
            const iconEl = document.getElementById(step.icon);
            iconEl?.classList.add('activo');

            // instrucción + cuenta regresiva
            if (instruccionDiv) instruccionDiv.innerText = step.texto;
            await _this.cuentaRegresiva(estado);

            // dibujar en canvas y mostrar
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            canvas.classList.remove('d-none');

            // canvas a blob
            const nombre = step.id + '_' + _this.timestampYYYYMMDD_HHMMSS();
            const blob = await new Promise((resolve) => {
            canvas.toBlob((b) => resolve(b), 'image/jpeg', 0.92);
            });
            if (!blob) throw new Error('No se pudo capturar blob');

            // guardar
            this.capturas.push({ nombre, blob });

            // feedback
            iconEl?.classList.remove('activo');
            iconEl?.classList.add('completado');
            if (estado) estado.innerText = "✔ Capturado";
            await this.delay(500);
        };

        try {
            // frente primero, luego resto
            await capturarPaso(_this.instrucciones[0]);
            for (let i = 1; i < _this.instrucciones.length; i++){
            await capturarPaso(this.instrucciones[i]);
            }

            // fin
            if (instruccionDiv) instruccionDiv.innerText = '✅ Captura completa';
            if (estado) estado.innerText = '';
            $('#btnRepetirSelfie').removeClass('d-none');

        } catch (err) {
            console.error(err);
            if (estado) estado.innerText = 'Error durante la captura: ' + (err?.message || err);
        } finally {
            btn.disabled = false;
        }
    }

    async finalizarSelfieYEnviar(){

        const _this = this
        // Verifica que exista selfie
        if (!this.capturas.length) {
            const estado = document.getElementById('estadoCaptura');
            if (estado) estado.innerText = "Por favor, toma la selfie antes de finalizar.";
            return;
        }

        // Cierra modal y cámara
        $('#tomarFotoModal').modal('hide');
        _this.detenerCamara();

        // Envía el formulario con la selfie adjunta
        _this.enviarFormulario();
    }


        buscaDistrito() {
            objComun.cargarSelectDependiente({
                origenId: "provincia",
                destinoId: "distrito",
                rutaAjax: "/dist/solicitud/buscaDistrito",
                parametro: "provincia",
                limpiarDestinoId: "corregimiento",
                mensajeCargando: 'Cargando...',
                mensajeDefault: 'Selecciona...',
            });
        }

        buscaCorregimiento(){
            objComun.cargarSelectDependiente({
                origenId: "distrito",
                destinoId: "corregimiento",
                rutaAjax: "/dist/solicitud/buscaCorregimiento",
                parametro: "distrito",
                mensajeCargando: 'Cargando...',
                mensajeDefault: 'Selecciona...',
            });
        }


        toggleViviendaFields() {

            const isCasa     = document.getElementById('tv_casa')?.checked === true;
            const isEdificio = document.getElementById('tv_edificio')?.checked === true;
            const isHotel    = document.getElementById('tv_hotel')?.checked === true;

            // Grupos
            const gCasa   = document.getElementById('grupoCasa');
            const gEdNom  = document.getElementById('grupoEdificio_nombre');
            const gEdPiso = document.getElementById('grupoEdificio_piso');
            const gEdApto = document.getElementById('grupoEdificio_apto');
            const gHotel  = document.getElementById('grupoHotel');

            // Inputs
            const iCasa  = document.getElementById('numero_casa');
            const iEdNom = document.getElementById('nombre_edificio');
            const iPiso  = document.getElementById('piso');
            const iApto  = document.getElementById('apartamento');
            const iHotel = document.getElementById('nombre_hotel');

            // Helper para mostrar/ocultar y required
            const show = (el) => el?.classList.remove('d-none');
            const hide = (el) => el?.classList.add('d-none');
            const req  = (input, on) => {
                if (!input) return;
                if (on) input.setAttribute('required', 'required');
                else    input.removeAttribute('required');
            };

            // 1) Ocultar todo y quitar required
            [gCasa, gEdNom, gEdPiso, gEdApto, gHotel].forEach(hide);
            [iCasa, iEdNom, iPiso, iApto, iHotel].forEach(inp => req(inp, false));

            // 2) Mostrar según selección y marcar required
            if (isCasa) {
                show(gCasa);
                req(iCasa, true);

                // Limpiar lo que NO aplica
                if (iEdNom) iEdNom.value = '';
                if (iPiso)  iPiso.value  = '';
                if (iApto)  iApto.value  = '';
                if (iHotel) iHotel.value = '';
            } else if (isEdificio) {
                show(gEdNom); show(gEdPiso); show(gEdApto);
                req(iEdNom, true); req(iPiso, true); req(iApto, true);

                if (iCasa)  iCasa.value  = '';
                if (iHotel) iHotel.value = '';
            } else if (isHotel) {
                show(gHotel);
                req(iHotel, true);

                if (iCasa)  iCasa.value  = '';
                if (iEdNom) iEdNom.value = '';
                if (iPiso)  iPiso.value  = '';
                if (iApto)  iApto.value  = '';
            }
        }

         toggleReciboFields() {
            const isMio      = document.getElementById('recibo_mio')?.checked === true;
            const isTercero  = document.getElementById('recibo_tercero')?.checked === true;

            // Grupos
            const gNotariado = document.getElementById('recibo_notariado_group');
            const gCedula    = document.getElementById('cedula_titular_group');

            // Inputs
            const iNotariado = document.getElementById('recibo_notariado_archivo');
            const iCedula    = document.getElementById('recibo_cedula_titular');

            // Helpers
            const show = (el) => el?.classList.remove('d-none');
            const hide = (el) => el?.classList.add('d-none');
            const req  = (input, on) => {
                if (!input) return;
                if (on) input.setAttribute('required', 'required');
                else    input.removeAttribute('required');
            };

            // 1) Ocultar y limpiar todo
            [gNotariado, gCedula].forEach(hide);
            [iNotariado, iCedula].forEach(inp => {
                req(inp, false);
                if (inp?.type === 'file') inp.value = ''; // limpiar archivo si no aplica
            });

            // 2) Mostrar según selección
            if (isTercero) {
                show(gNotariado); show(gCedula);
                req(iNotariado, true);
                req(iCedula, true);
            }
        }


        toggleDocsByDomicilio() {

            const _this = this

            const show  = (el) => el?.classList.remove('d-none');
            const hide  = (el) => el?.classList.add('d-none');
            const req   = (input, on) => { if (!input) return; on ? input.setAttribute('required','required') : input.removeAttribute('required'); };
            const dis   = (input, on) => { if (!input) return; input.disabled = !!on; };
            const clearFile = (input) => { if (input && input.type === 'file') input.value = ''; };


            const isHotel = document.getElementById('dom_reservahotel')?.checked === true;

            // Bloque Recibo
            const gRecibo = document.getElementById('grupoRecibo');

            // Inputs dentro del bloque Recibo
            const iReciboFile      = document.getElementById('recibo_archivo');
            const iReciboMio       = document.getElementById('recibo_mio');
            const iReciboTercero   = document.getElementById('recibo_tercero');
            const iNotariado       = document.getElementById('recibo_notariado_archivo');
            const iCedulaTitular   = document.getElementById('recibo_cedula_titular');

            // Subgrupos para poder ocultarlos también si hace falta
            const gNotariado = document.getElementById('recibo_notariado_group');
            const gCedula    = document.getElementById('cedula_titular_group');

            // Nota bajo “Prueba de domicilio”
            const nota = document.getElementById('domicilioNota');

            if (isHotel) {
                // 1) Ocultar bloque
                hide(gRecibo);

                // 2) Quitar required y deshabilitar TODOS los campos del bloque
                [iReciboFile, iNotariado, iCedulaTitular].forEach(inp => {
                req(inp, false);
                clearFile(inp);
                dis(inp, true);
                });
                [iReciboMio, iReciboTercero].forEach(inp => dis(inp, true));

                // 3) Asegurar que los subgrupos dependientes no quedan visibles
                hide(gNotariado); hide(gCedula);

                // 4) Mensaje guía
                if (nota) nota.textContent = 'Si presenta reserva de hotel, no necesita recibo de servicio.';
            } else {
                // 1) Mostrar bloque
                show(gRecibo);

                // 2) Habilitar campos
                [iReciboFile, iNotariado, iCedulaTitular, iReciboMio, iReciboTercero].forEach(inp => dis(inp, false));

                // 3) Required por defecto del recibo principal
                req(iReciboFile, true);

                // 4) Volver a evaluar propio/tercero para pedir lo que corresponda
                _this.toggleReciboFields();

                // 5) Mensaje guía por defecto
                if (nota) nota.textContent = 'Requiere notaría.';
            }
        }

        preSubmitCheck() {

            const _this = this

            console.log('Ejecutando preSubmitCheck');

            // Asegurar visibilidad correcta antes de validar
            this.toggleViviendaFields();
            this.toggleReciboFields();
            this.toggleDocsByDomicilio();

            // --- helpers visuales ---
            const clearErrors = () => {
                $("#formErrorList").empty();
                $("#formErrorBox").addClass("d-none");
                $("#nuevoregistro .is-invalid").removeClass("is-invalid").removeAttr("aria-invalid");
                $("#nuevoregistro .invalid-feedback.js-inline").remove();
            };

            const addError = (msg) => {
                $("#formErrorList").append(`<li>${msg}</li>`);
            };

            // registra una sola vez listeners para limpiar el error al corregir
            const attachLiveValidation = (selector) => {
                const $el = $(selector);
                if (!$el.length) return;
                if ($el.data('live-bound')) return; // evita duplicar listeners
                $el.data('live-bound', true);

                const type = ($el.attr('type') || '').toLowerCase();

                // texto/number/select: quitar rojo al escribir/cambiar
                $el.on('input change', function () {
                $(this).removeClass('is-invalid').removeAttr('aria-invalid');
                const $fb = $(this).next('.invalid-feedback.js-inline');
                if ($fb.length) $fb.remove();
                });

                // archivos: change basta
                if (type === 'file') {
                $el.on('change', function () {
                    $(this).removeClass('is-invalid').removeAttr('aria-invalid');
                    const $fb = $(this).next('.invalid-feedback.js-inline');
                    if ($fb.length) $fb.remove();
                });
                }
            };

            const requireField = (selector, message) => {
                const $el = $(selector);
                attachLiveValidation(selector);
                if (!$el.length) return true;

                const isFile = ($el.attr("type") || "").toLowerCase() === "file";
                const valOk = isFile ? ($el[0].files && $el[0].files.length > 0)
                                    : !!$el.val()?.toString().trim();

                if (!valOk) {
                $el.addClass("is-invalid").attr("aria-invalid", "true");
                // feedback inline (asegura texto rojo visible)
                const $next = $el.next(".invalid-feedback");
                if ($next.length === 0 || !$next.hasClass('js-inline')) {
                    $el.after(`<div class="invalid-feedback js-inline d-block">${message}</div>`);
                } else {
                    $next.addClass("d-block").text(message);
                }
                addError(message);
                return false;
                }
                return true;
            };

            const showErrors = () => {
                $("#formErrorBox").removeClass("d-none");
                document.getElementById("formErrorBox")
                .scrollIntoView({ behavior: "smooth", block: "start" });
                const $firstInvalid = $("#nuevoregistro .is-invalid").first();
                if ($firstInvalid.length) $firstInvalid.trigger("focus");
            };

            // --- iniciar ---
            clearErrors();
            let hasErrors = false;
            const flag = (bad) => { if (bad) hasErrors = true; };

            // === Ubicación ===
            flag(!requireField("#provincia", "Debe seleccionar la provincia."));
            flag(!requireField("#distrito", "Debe seleccionar el distrito."));
            flag(!requireField("#corregimiento", "Debe seleccionar el corregimiento."));

            // === Dirección específica ===
            flag(!requireField("#barrio", "Debe ingresar el barrio o urbanización."));
            flag(!requireField("#calle", "Debe ingresar la calle o avenida."));
            flag(!requireField("#punto_referencia", "Debe ingresar un punto de referencia."));

            // === Tipo de vivienda (NO pintamos los radios; solo sus campos) ===
            const isCasa     = document.getElementById("tv_casa")?.checked === true;
            const isEdificio = document.getElementById("tv_edificio")?.checked === true;
            const isHotel    = document.getElementById("tv_hotel")?.checked === true;

            if (isCasa) {
                flag(!requireField("#numero_casa", "Debe ingresar el número de casa."));
            }
            if (isEdificio) {
                flag(!requireField("#nombre_edificio", "Debe ingresar el nombre del edificio."));
                flag(!requireField("#piso", "Debe ingresar el piso."));
                flag(!requireField("#apartamento", "Debe ingresar el apartamento."));
            }
            if (isHotel) {
                flag(!requireField("#nombre_hotel", "Debe ingresar el nombre del hotel."));
            }

            // === Documento de domicilio (siempre) ===
            flag(!requireField("#domicilio_archivo", "Debe adjuntar el documento de domicilio."));

            // === Recibo de servicio (si NO es reserva de hotel) ===
            const isReservaHotel = document.getElementById("dom_reservahotel")?.checked === true;
            if (!isReservaHotel) {
                flag(!requireField("#recibo_archivo", "Debe adjuntar el recibo de servicio."));
                const isTercero = document.getElementById("recibo_tercero")?.checked === true;
                if (isTercero) {
                flag(!requireField("#recibo_notariado_archivo", "Debe adjuntar el comprobante notariado del recibo de tercero."));
                flag(!requireField("#recibo_cedula_titular", "Debe adjuntar la cédula del titular del recibo (frente y reverso)."));
                }
            }

            // === Carnet migratorio (siempre) ===
            flag(!requireField("#carnet_frente", "Debe adjuntar el carnet migratorio (anverso)."));
            flag(!requireField("#carnet_reverso", "Debe adjuntar el carnet migratorio (reverso)."));

            // resultado
            if (hasErrors) {
                showErrors();
                return;
            }

            $("#tomarFotoModal").modal("show");          

            // _this.enviarFormulario();             

        }

        enviarFormulario() {

            console.log('Por Aqui vamos 2');

            const $form = $("#nuevoregistro");
            const formData = new FormData($form[0]);

            $.ajax({
                url: $form.attr("action"),   // apunta a /dist/solicitud/nuevo
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (resp) {
                    console.log("Respuesta servidor:", resp);

                    const objMessagebasicModal = new MessagebasicModal(
                        "Solicitud",
                        "Se ha enviado correctamente la información."
                    );
                    objMessagebasicModal.init();
                },
                error: function (xhr) {
                    console.error(xhr);
                    const objMessagebasicModal = new MessagebasicModal(
                        "Error",
                        "Hubo un problema al enviar la solicitud."
                    );
                    objMessagebasicModal.init();
                }
            });
        
            // const m = new MessagebasicModal(
            //     'Validación',
            //     'Por favor corrige los campos marcados antes de continuar.',
            //     { type: 'warning' } // opcional: info | warning | danger | success | primary
            // );

            // m.init();
        }

        /*BEGIN TABLA USUARIO*/
        solicitud(search){

            //var BASEURL = window.location.origin; 

            console.log(BASEURL);

            const _this = this

            const table = $('#solicitud').DataTable( {
                "destroy": true,
                "searching": false,
                "serverSide": true,
                "autoWidth": false,
                "info": true,
                "lengthMenu": objComun.lengthMenuDataTable,
                "pageLength": pageLengthDataTable, //Variable global en el layout
                "language": {
                    "lengthMenu": "Mostrar _MENU_ por página",
                    "zeroRecords": "No se ha encontrado información",
                    "info": "Mostrando _PAGE_ de _PAGES_",
                    "infoEmpty": "",
                },
                "ajax": {
                    "url":BASEURL,
                    "type": "POST",
                    "error": this.handleAjaxError, 
                    "data": function ( d ) {
                        var info = $('#solicitud').DataTable().page.info();
                        
                        var orderColumnNumber = d.order[0].column;
                        
                        objComun.orderDirReporte = d.order[0].dir; //Variable global en comun.js
                        objComun.orderColumnReporte = d.columns[orderColumnNumber].data; //Variable global en comun.js
                        objComun.lengthActualReporte = info.length; //Variable global en comun.js
                        objComun.paginaActualReporte = info.page+1; //Variable global en comun.js
                        
                        d.currentPage = info.page + 1;
                        d.searchInput = search;
                        d._token=token;
                    }
                },
                "columns": [
                    { "data": "id"},
                    { "data": "nombre" },
                    { "data": "ruex"},
                    { "data": "codigo" },
                    { "data": "direccion" },
                    // { "data": "estatus" },
                    {
                        "data": "estatus",
                        render: function (data) {
                            return _this.getBadgeEstatus(data);
                        }
                    },

                    { "data": "detalle" , "orderable": false, className: "actions text-end"},
                ],

                "initComplete": function (settings, json) {

                },
                "infoCallback": function( settings, start, end, max, total, pre ) {

                    _this.desactivarsolicitud();

                    var api = this.api();
                    var pageInfo = api.page.info();
                    return 'Mostrando '+ (pageInfo.page+1) +' de '+ pageInfo.pages;
                }
            });  
        }

        handleAjaxError( xhr, textStatus, error ) {
            console.log(error);
        }

          /*END TABLA USUARIO*/

          /*BEGIN VALIDAR NUEVO USUARIO*/
          validatesolicitud(){

            $('#nuevoregistro').submit(function(){
                $(this).find(':submit').attr('disabled','disabled');
              });
            //console.log('por aqui vamos')

              $("#nuevoregistro").validate({
                  submitHandler: function(form) {
                      console.log('submit');
                      form.submit();
                   },
                   invalidHandler:function(form) {
                   },
                   highlight: function(element) {
                    $('#submitForm').removeAttr("disabled");

                       var titleElemnt = $( element ).attr( "id" );
                       $("button[data-id*='"+titleElemnt+"']").addClass( "errorValidate" );
                       $(element).addClass( "errorValidate" );
                    },
                   unhighlight: function(element) {
                       var titleElemnt = $( element ).attr( "id" );
                       $("button[data-id*='"+titleElemnt+"']").addClass( "successValidate" );
                       $(element).addClass( "successValidate" ); 
                    },
                  rules: {
                    nombre: {
                        required: false,
                    },
                   
                  },
                  messages: {
                    regional: {
                        required: "",
                    },
                   
                  }
              });
          }

          validateimportacion(){

            var validator = $("#nuevoimportar").validate({
                ignore: [],
                submitHandler: function(form) {
                    form.submit();
                },
                invalidHandler:function(form) {
                },
                errorPlacement: function(error, element) {
                    if (element.attr("name") == "archivoPlantilla") {
                    error.appendTo('#fileError');
                    } else {
                    error.insertAfter(element);
                    }
                },
                rules: {
                    archivoPlantilla: {
                        required: true,
                        extension: "xls|xlsx",
                        filesize: 1048576
                    }
                },
                messages: {
                    archivoPlantilla: {
                        required: "Este elemento es requerido",
                        extension: "Solo xls o xlsx",
                        filesize: "Tamaño plantilla incorrecto"
                    }
                }
            });

            $.validator.addMethod('filesize', function(value, element, param) {
                // param = size (in bytes) 
                // element = element to validate (<input>)
                // value = value of the element (file name)
                return this.optional(element) || (element.files[0].size <= param) 
            });	
            
        }

            desactivarsolicitud(){

                const _this = this

                $( ".desactivar" ).off('click');
                    $( ".desactivar" ).click(function() {
                        
                        const solicitudId = $( this ).attr( "attr-id" );
                        var opciones = {solicitudId:solicitudId};
                        const message = 'Seguro que desea cambiar de estatus el solicitud?'
                        const objConfirmacionmodal = new Confirmacionmodal(message, opciones, _this.callbackDesactivarsolicitud);
                    objConfirmacionmodal.init();
                    
                });
            }
          

          callbackDesactivarsolicitud(response, opciones){

   
              if(response == true){

                  const _this = this;

                  $.post( BASEURL+'/desactivar', 
                  {
                      solicitudId: opciones.solicitudId,
                      _token:token 
                      }
                  )
                  .done(function( data ) {

                    console.log('por aqui vamos callbackDesactivarsolicitud')


                      if(data.response == true){
                          const modalTitle = 'Solicitud';
                          const modalMessage = 'El solicitud ha sido cambiado de estatus';
                          const objMessagebasicModal = new MessagebasicModal(modalTitle, modalMessage);
                          objMessagebasicModal.init();

                          const objDistsolicituds = new Distsolicitud();							
                          objDistsolicituds.init();	
                          //objDistsolicituds.solicituds($( "#search" ).val());

                      }else{
                          
                          const modalTitle = 'Solicitud';
                          const modalMessage = 'El solicitud no se ha podido cambiar de estatus';
                          const objMessagebasicModal = new MessagebasicModal(modalTitle, modalMessage);
                          objMessagebasicModal.init();

                          //const objDistsolicituds = new Distsolicitud();							
                          //objDistsolicituds.solicituds($( "#search" ).val());
                      }
                  })
                  .fail(function() {
                      
                      const modalTitle = 'Solicitud';
                      const modalMessage = 'El solicitud no se ha podido cambiar de estatus';
                      const objMessagebasicModal = new MessagebasicModal(modalTitle, modalMessage);
                      objMessagebasicModal.init();

                      //const objDistsolicituds = new Distsolicitud();							
                      //objDistsolicituds.solicituds($( "#search" ).val());
                      
                  })
                  .always(function() {
                      
                  }, "json");

              }
              
          }

          /*END DESACTIVAR UN DISTRIBUIDOR*/



  }


$(document).ready(function(){
  const objDistsolicitud = new Distsolicitud();
  objDistsolicitud.init();
});

// document.addEventListener("DOMContentLoaded", () => {
//   const objDistsolicitud = new Distsolicitud();
//   objDistsolicitud.init();
// });

