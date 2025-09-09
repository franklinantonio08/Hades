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
            'Por revisión':  'bg-info bg-gradient',
            'Por corregir': 'bg-warning bg-gradient',
            'Aprobada - con pago':  'bg-success bg-gradient',
            'Aprobada - sin pago':  'bg-success bg-gradient',
            'Multa emitida':    'bg-primary bg-gradient',
            'Rechazada':   'bg-danger bg-gradient',
            'Cancelada':   'bg-dark bg-gradient',
            'default':   'bg-light bg-gradient text-dark'
        };

        this.STEPS = [
            'Recibida',
            'Por revisión',
            'Por corregir',
            'Aprobada - con pago',
            'Aprobada - sin pago',
            'Multa emitida',
            'Rechazada',
            'Cancelada'
        ];     

    }

    getStatusOfficial(estatus) {
        const norm = s => (s || '').normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase().trim();
        const c = norm(estatus);

        // Aliases para datos viejos o variaciones
        const alias = {
            'en revision': 'Por revisión',
            'en revisión': 'Por revisión',
            'por revisar': 'Por revisión',
            'por corregir': 'Por corregir',
            'aprobada con pago': 'Aprobada - con pago',
            'aprobada sin pago': 'Aprobada - sin pago',
            'multa emitida': 'Multa emitida',
            'observada': 'Por corregir' // si tenías "Observada" antes, la llevamos a "Por corregir"
        };

        return alias[c] || this.STEPS.find(s => norm(s) === c) || 'Recibida';
    }

    getFlowForStatus(oficial) {
        // Muestra SOLO la rama relevante
        switch (oficial) {
            case 'Recibida':
            case 'Por revisión':
            return ['Recibida', 'Por revisión'];

            case 'Por corregir':
            return ['Recibida', 'Por revisión', 'Por corregir'];

            case 'Aprobada - sin pago':
            return ['Recibida', 'Por revisión', 'Aprobada - sin pago'];

            case 'Aprobada - con pago':
            // mostremos el paso final esperado
            return ['Recibida', 'Por revisión', 'Aprobada - con pago', 'Multa emitida'];

            case 'Multa emitida':
            return ['Recibida', 'Por revisión', 'Aprobada - con pago', 'Multa emitida'];

            case 'Rechazada':
            return ['Recibida', 'Por revisión', 'Rechazada'];

            case 'Cancelada':
            return ['Recibida', 'Por revisión', 'Cancelada'];

            default:
            return ['Recibida', 'Por revisión'];
        }
    }


    renderTimeline(estatus) {
        const oficial = this.getStatusOfficial(estatus);
        const steps = this.getFlowForStatus(oficial);

        const reached = Math.max(0, steps.indexOf(oficial));
        const pct = steps.length > 1 ? Math.round(reached * 100 / (steps.length - 1)) : 0;

        const isDanger = ['Rechazada','Cancelada'].includes(oficial);
        const isWarn   = oficial === 'Por corregir';

        const pillClass = isDanger
            ? 'bg-danger-subtle text-danger border border-danger-subtle'
            : (isWarn
                ? 'bg-warning-subtle text-warning border border-warning-subtle'
                : 'bg-success-subtle text-success border border-success-subtle');

        const fillColor = isDanger ? '#dc3545' : (isWarn ? '#ffc107' : '#198754');

        let stepsHtml = '';
        steps.forEach((etapa, i) => {
            const done = i <= reached;
            const current = i === reached;
            const dotStyle = done ? `style="background:${fillColor}; border-color:${fillColor};"` : '';
            stepsHtml += `
            <div class="t-step ${done ? 'done' : ''} ${current ? 'current' : ''}">
                <span class="t-dot" ${dotStyle}>${done ? '<i class="bi bi-check-lg"></i>' : ''}</span>
                <span class="t-label" data-bs-toggle="tooltip" data-bs-placement="bottom" title="${etapa}">${etapa}</span>
            </div>`;
        });

        return `
            <div class="t-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="badge rounded-pill ${pillClass}">Estatus: ${oficial}</span>
                <small class="text-muted">Progreso: ${pct}%</small>
            </div>

            <div class="tline">
                <div class="tline-bg"></div>
                <div class="tline-fill" style="width:${pct}%; background:${fillColor}"></div>
                <div class="tsteps">${stepsHtml}</div>
            </div>
            </div>`;
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

        document.getElementById('modalRegistro')?.addEventListener('hidden.bs.modal', () => {
            resetResultados();
        });

    
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

        $('#nuevoRegistroBtn').off('click').on('click', function() {
            _this.validaSolicitud();
        });


        $('#btnBuscar').off('click').on('click', function (e) {

            e.preventDefault();
            _this.BuscaFamiliar();
            console.log('por aqui vamos');
        });

        $('#btnLimpiar').off('click').on('click', function () {
            $('#nombre, #apellido, #ruex, #fecha_nacimiento').val('');
            $('#genero, #afinidadId').val('');
            _this.resetResultados();
        });

        $(document).off('click', '.seleccionar-familiar').on('click', '.seleccionar-familiar', function () {  
            // $('#nombre, #apellido, #ruex, #fecha_nacimiento').val('');
            // $('#genero, #afinidadId').val('');
            // _this.resetResultados();
            console.log('Por Aqui vamos');
            _this.selecionarFamiliar();
        });

    
    }

    resetResultados() {
        $('#tablaResultados tbody').empty();
        $('#DivResultado_busqueda').addClass('d-none');
        $('#modalRegistroDesc')
            .removeClass('text-danger')
            .text('Completa uno o más criterios y presiona Buscar.');
    }

    selecionarFamiliar(){

        const documento  = $('#ruex').val(); 
        const afinidadId = $('#afinidadId').val(); 

        console.log(documento);
        console.log(afinidadId);

        if (!afinidadId) {
            alert('Selecciona una afinidad antes de continuar.');
            return;
        }

        // UI: loading
        $('#buscarSpinner').removeClass('d-none');
        $('#buscarEstado').removeClass('d-none').text('Cargando…');

        // POST solo con { documento, afinidad_id }
        $.ajax({
            url: `${BASEURL}/seleccion-familiar`,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': token }, // usa tu variable global 'token'
            data: { documento: documento, afinidad_id: afinidadId }
        })
        .done(function (res) {
            if (res && res.ok && res.redirect) {
            // /dist/solicitud/nuevo?sel=UUID (token efímero)
            window.location.href = res.redirect;
            } else {
            alert(res?.msg || 'No fue posible seleccionar a la persona.');
            }
        })
        .fail(function () {
            alert('Error al seleccionar a la persona.');
        })
        .always(function () {
            $('#buscarSpinner').addClass('d-none');
            $('#buscarEstado').addClass('d-none').text('');
        });

    }

    BuscaFamiliar(){

        const _this = this

        const payload = {
            nombre: $('#nombre').val().trim(),
            apellido: $('#apellido').val().trim(),
            ruex: $('#ruex').val().trim(),
            genero: $('#genero').val(),
            fecha_nacimiento: $('#fecha_nacimiento').val(),
            afinidadId: $('#afinidadId').val() // cuando lo uses
        };

        if (!payload.nombre && !payload.apellido && !payload.ruex && !payload.genero && !payload.fecha_nacimiento) {
            $('#modalRegistroDesc').addClass('text-danger').text('Indica al menos un criterio de búsqueda.');
            setTimeout(() => {
            $('#modalRegistroDesc').removeClass('text-danger').text('Completa uno o más criterios y presiona Buscar.');
            }, 2500);
            return;
        }

        $('#buscarSpinner').removeClass('d-none');
        $('#buscarEstado').removeClass('d-none').text('Buscando…');
        $('#btnBuscar').prop('disabled', true);

        $.ajax({
            url: `${BASEURL}/buscafamiliar`,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': _this.getCsrfToken() },
            data: payload,
            dataType: 'json'
        })
        .done(function (res) {
            const $tbody = $('#tablaResultados tbody');
            $tbody.empty();

            if (res && res.ok && Array.isArray(res.data) && res.data.length > 0) {
            res.data.forEach((item, idx) => {
                const fila = `
                <tr>
                    <td class="text-center">${idx + 1}</td>
                    <td>${_this.escapeHtml(item.nombre)}</td>
                    <td class="text-center">${_this.escapeHtml(item.documento)}</td>
                    <td class="text-center">${_this.escapeHtml(item.genero)}</td>
                    <td class="text-center">${_this.escapeHtml(item.nacionalidad || '—')}</td>
                    <td class="text-center">${_this.escapeHtml(item.fecha_nacimiento)}</td>
                    <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-primary seleccionar-familiar"
                            data-documento="${encodeURIComponent(item.documento)}">
                        <i class="bi bi-check2-circle"></i> Seleccionar
                    </button>
                    </td>
                </tr>`;
                $tbody.append(fila);
            });

            $('#DivResultado_busqueda').removeClass('d-none');
            } else {
            const msg = (res && res.empty) ? (res.msg || 'Sin resultados') : 'Sin resultados';
            $tbody.append(`
                <tr>
                <td class="text-center" colspan="7">${_this.escapeHtml(msg)}</td>
                </tr>
            `);
            $('#DivResultado_busqueda').removeClass('d-none');
            }
        })
        .fail(function () {
            const $tbody = $('#tablaResultados tbody');
            $tbody.empty().append(`
            <tr>
                <td class="text-center text-danger" colspan="7">
                Ocurrió un error al buscar. Intenta nuevamente.
                </td>
            </tr>
            `);
            $('#DivResultado_busqueda').removeClass('d-none');
        })
        .always(function () {
            $('#buscarSpinner').addClass('d-none');
            $('#buscarEstado').addClass('d-none').text('');
            $('#btnBuscar').prop('disabled', false);
        });


    }

    escapeHtml(text) {
        if (text === null || text === undefined) return '';
        return String(text)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    getCsrfToken() {
        return $('#csrfToken').val() || $('input[name="_token"]').val();
    }

    validaSolicitud() {
       
        fetch(BASEURL + "/validar-solicitud", {
            method: "POST",
            headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": token },
            body: JSON.stringify({})
        })
        .then(r => r.json())
        .then(data => {
            if (!data.ok) {
            if (data.error === 'SIN_RUEX' || data.error === 'SIN_IDENTIFICADOR') {
                alert(data.message || 'Debes registrar tu Ruex/documento.');
            }
            return;
            }

            const ui = data.ui || {};
            const tipo = (data.tipo_usuario || '').toString().trim().toLowerCase(); // <- normalizado
            const tieneActiva = !!data.tieneActiva;

            const modalEl = document.getElementById('modalElegirTipoTramite');
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl, { backdrop: 'static', keyboard: false });

            // textos
            document.getElementById('tipoTramiteTitle').textContent = ui.title || 'Tipo de trámite';
            document.getElementById('tipoTramiteBody').textContent  = ui.body  || 'Elige cómo continuar.';
            const notesEl = document.getElementById('tipoTramiteNotes');
            if (notesEl) {
            if (ui.notes) { notesEl.textContent = ui.notes; notesEl.classList.remove('d-none'); }
            else { notesEl.classList.add('d-none'); }
            }

            // helper para botones
            const setBtn = (btn, cfg) => {
            if (!btn) return;
            if (!cfg) { btn.classList.add('d-none'); return; } // si no viene del back => oculto
            btn.classList.remove('d-none');
            const span = btn.querySelector('span');
            if (span && cfg.label) span.textContent = cfg.label;
            btn.disabled = !(cfg.enabled ?? false);
            };

            const btnMi  = document.getElementById('btnParaMi');
            const btnFam = document.getElementById('btnParaFamiliar');
            const btnRep = document.getElementById('btnParaRepresentado');
            const btnCan = document.getElementById('btnCancelar');

            setBtn(btnMi,  ui.buttons?.mi);
            setBtn(btnFam, ui.buttons?.familiar);
            setBtn(btnRep, ui.buttons?.representado);
            if (btnCan && ui.buttons?.cancel?.label) btnCan.textContent = ui.buttons.cancel.label;

            // Cinturón adicional (por si acaso):
if (tipo === 'abogado') {
  btnMi?.remove();
  btnFam?.remove();
}
if (tipo === 'solicitante' && tieneActiva) {
  btnMi?.remove(); // <- quítalo del DOM, no solo d-none
}

            // evitar listeners duplicados
            const bindOnce = (el, fn) => {
            if (!el || el.classList.contains('d-none')) return;
            const clone = el.cloneNode(true);
            el.replaceWith(clone);
            clone.addEventListener('click', fn, { once: true });
            };

            const abrirModalRegistro = (tipoTramite, titulo) => {
            const modalRegistroEl = document.getElementById('modalRegistro');
            if (!modalRegistroEl) return;
            document.getElementById('tramite_tipo')?.setAttribute('value', tipoTramite);
            const tituloEl = document.getElementById('modalRegistroTitulo');
            if (tituloEl) tituloEl.textContent = titulo;

            const modalRegistro = bootstrap.Modal.getOrCreateInstance(modalRegistroEl, { backdrop:'static', keyboard:false });
            modalRegistro.show();
            setTimeout(() => document.getElementById('nombre')?.focus(), 200);
            };

            bindOnce(document.getElementById('btnParaMi'), () => {
            modal.hide();
            window.location.href = BASEURL + "/nuevo";
            });

            bindOnce(document.getElementById('btnParaFamiliar'), () => {
            modal.hide();
            abrirModalRegistro('familiar', 'Tramitar para un familiar');
            });

            bindOnce(document.getElementById('btnParaRepresentado'), () => {
            modal.hide();
            abrirModalRegistro('representado', 'Tramitar para un representado');
            });

            modal.show();
        })
        .catch(console.error);

    }

    /*validaSolicitud() {

        fetch(BASEURL + "/validar-solicitud", {
            method: "POST",
            headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": token
            },
            body: JSON.stringify({})
        })

        .then(r => r.json())

        .then(data => {
            if (data.tieneActiva) {

            const modalActivaEl = document.getElementById('modalSolicitudActiva');
            const modalActiva = bootstrap.Modal.getOrCreateInstance(modalActivaEl);
            modalActiva.show();

            const btnFamiliar = document.getElementById('btnTramitarFamiliar');

                if (btnFamiliar) {
                    btnFamiliar.addEventListener('click', () => {

                    modalActiva.hide();

                    const modalRegistroEl = document.getElementById('modalRegistro');
                    if (!modalRegistroEl) {
                      
                        return;
                    }
                    const modalRegistro = bootstrap.Modal.getOrCreateInstance(modalRegistroEl, { backdrop: 'static', keyboard: false });
                    modalRegistro.show();

                    setTimeout(() => {
                        const inputNombre = document.getElementById('nombre');
                        if (inputNombre) inputNombre.focus();
                    }, 250);
                    }, { once: true });
                }
            } else {

            window.location.href = BASEURL + "/nuevo";

            }

        })

        .catch(err => {
            console.error("Error:", err);
        });

    }*/

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

                    $('#messageBasicModal').on('hidden.bs.modal', function () {
                        window.location.href = '/dist/solicitud'; // 🔄 redirige al inicio
                    });
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
                    _this.mostrarsolicitud();

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

        mostrarsolicitud(){

            const _this = this;

            $(document).off('click', '.mostrar').on('click', '.mostrar', function() {
                const multasId = $(this).attr("attr-id");

                $.ajax({
                    url: `solicitud/mostrar/${multasId}`,
                    method: "GET",
                    success: function (data) {
                        if (data.error) { alert(data.error); return; }

                        $("#estado-progress").html(_this.renderTimeline(data.estatus));

                        document.querySelectorAll('#solicitudModal [data-bs-toggle="tooltip"]').forEach(el => {
                            const t = bootstrap.Tooltip.getInstance(el);
                            if (t) t.dispose();
                            new bootstrap.Tooltip(el);
                        });

                        const modalContent = `
                            <table class="table table-sm align-middle mb-0">
                            <tr><td><strong>Nombre Completo:</strong></td><td>${data.nombre_completo ?? ''}</td></tr>
                            <tr><td><strong>Documento:</strong></td><td>${data.num_filiacion ?? ''}</td></tr>
                            <tr><td><strong>Provincia:</strong></td><td>${data.provincia ?? ''}</td></tr>
                            <tr><td><strong>Distrito:</strong></td><td>${data.distrito ?? ''}</td></tr>
                            <tr><td><strong>Corregimiento:</strong></td><td>${data.corregimiento ?? ''}</td></tr>
                            <tr><td><strong>Barrio / Urbanización:</strong></td><td>${data.barrio ?? ''}</td></tr>
                            <tr><td><strong>Calle / Avenida:</strong></td><td>${data.calle ?? ''}</td></tr>
                            <tr><td><strong>N° de casa:</strong></td><td>${data.numero_casa ?? ''}</td></tr>
                            <tr><td><strong>Punto de referencia:</strong></td><td>${data.punto_referencia ?? ''}</td></tr>
                            <tr><td><strong>Estado:</strong></td><td>${data.estatus ?? ''}</td></tr>
                            </table>`;
                        $("#modal-content").html(modalContent);

                        $("#solicitudModal").modal("show");
                    },

                    error: function (xhr) {
                        alert("Error al cargar los datos. Intente nuevamente.");
                        console.error(xhr);
                    },
                });
            });

            // Limpia tooltips al cerrar (evita ghosts si reabres)
            const modalEl = document.getElementById('solicitudModal');
            if (modalEl && !modalEl._tooltipCleanupBound) {
                modalEl.addEventListener('hidden.bs.modal', () => {
                document.querySelectorAll('#solicitudModal .tooltip.show').forEach(t => t.remove());
                });
                modalEl._tooltipCleanupBound = true;
            }
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

