class Authregistro {    

    constructor() {
    }

    init(){
        
        if($('#registerForm').length) {

            console.log('Por aqui vamos');
            //this.cambia_posiciones();

            this.bindTogglePassword();
            this.bindCapsLockHint();
            this.bindUserTypeToggle();
            this.bindIdoneidadValidation();

            //this.bindFormAjax()
            
        }    
     
        this.acciones();      

    }

    acciones(){

        const _this = this;    

    }


    /* ====== UI: Mostrar / ocultar contraseña ====== */
    bindTogglePassword() {
        const pwd  = document.getElementById('password');
        const btn  = document.getElementById('togglePwd');
        const icon = document.getElementById('toggleIcon');
        if (!pwd || !btn || !icon) return;

        btn.addEventListener('click', () => {
        const showing = pwd.getAttribute('type') === 'text';
        pwd.setAttribute('type', showing ? 'password' : 'text');
        icon.classList.toggle('bi-eye');
        icon.classList.toggle('bi-eye-slash');
        pwd.focus({ preventScroll: true });
        });
    }

    /* ====== UI: Hint de Bloq Mayús ====== */
    bindCapsLockHint() {
        const pwd  = document.getElementById('password');
        const hint = document.getElementById('capsHint');
        if (!pwd || !hint) return;

        const update = (e) => {
        const caps = e.getModifierState && e.getModifierState('CapsLock');
        hint.style.display = caps ? 'block' : 'none';
        };
        ['keyup','keydown','focus'].forEach(ev => pwd.addEventListener(ev, update));
        pwd.addEventListener('blur', () => hint.style.display = 'none');
    }

    /* ====== UI: Toggle secciones + required dinámicos ====== */
    bindUserTypeToggle() {
        const radios   = document.querySelectorAll('input[name="tipo_usuario"]');
        const secSolic = document.getElementById('sectionSolicitante');
        const secAbog  = document.getElementById('sectionAbogado');
        if (!radios.length || !secSolic || !secAbog) return;

        const camposSolicReq = ['documento_tipo','documento_numero'];
        const camposAbogReq  = ['abogado_id','idoneidad'];

        const setRequired = (ids, value) => {
        ids.forEach(id => {
            const el = document.getElementById(id);
            if (!el) return;
            value ? el.setAttribute('required', 'required') : el.removeAttribute('required');
        });
        };

        const paint = () => {
        const val = document.querySelector('input[name="tipo_usuario"]:checked')?.value || 'solicitante';
        const isAbogado = (val === 'abogado');
        secAbog.style.display  = isAbogado ? 'block' : 'none';
        secSolic.style.display = isAbogado ? 'none'  : 'block';
        setRequired(camposAbogReq,  isAbogado);
        setRequired(camposSolicReq, !isAbogado);
        };

        radios.forEach(r => r.addEventListener('change', paint));
        paint(); // estado inicial
    }

    /* ====== UI: Validación de archivo idoneidad (tipo/tamaño) ====== */
    bindIdoneidadValidation() {
        const input = document.getElementById('idoneidad');
        if (!input) return;

        const max   = parseInt(input.dataset.max || 5242880, 10); // 5MB
        const valid = ['application/pdf','image/jpeg','image/png'];
        const fb    = document.getElementById('idoneidadFeedback');

        input.addEventListener('change', () => {
        if (fb) fb.textContent = '';
        input.classList.remove('is-invalid');

        const file = input.files && input.files[0];
        if (!file) return;

        if (file.size > max) {
            if (fb) fb.textContent = 'El archivo excede 5MB.';
            input.classList.add('is-invalid');
            input.value = '';
            return;
        }
        if (!valid.includes(file.type)) {
            if (fb) fb.textContent = 'Formato no permitido. Usa PDF/JPG/PNG.';
            input.classList.add('is-invalid');
            input.value = '';
        }
        });
    }


    bindFormAjax() {
        const form = document.getElementById('registerForm');
        const btn  = document.getElementById('btnRegister');
        const alertBox = document.getElementById('ajaxAlert');

        if (!form) return;

        const setAlert = (type, msg) => {
        alertBox.className = 'alert alert-' + type;
        alertBox.textContent = msg;
        alertBox.classList.remove('d-none');
        };
        const clearAlert = () => {
        alertBox.className = 'alert d-none';
        alertBox.textContent = '';
        };

        const clearFieldErrors = () => {
        $('#registerForm .is-invalid').removeClass('is-invalid');
        $('#registerForm .invalid-feedback[data-js="1"]').remove();
        };

        const setFieldError = (field, message) => {
        const $input = $(`#registerForm [name="${field}"]`);
        if (!$input.length) return;

        $input.addClass('is-invalid');
        // Busca feedback existente en el contenedor cercano
        let $fb = $input.closest('.input-group, .col-12, .mb-3, .form-group').find('.invalid-feedback').first();
        if (!$fb.length) {
            $fb = $('<div class="invalid-feedback" data-js="1"></div>');
            $input.closest('.input-group, .col-12, .mb-3, .form-group').append($fb);
        }
        $fb.text(message);
        };

        const setLoading = (loading) => {
        if (!btn) return;
        btn.disabled = loading;
        btn.querySelector('.btn-label')?.classList.toggle('d-none', loading);
        btn.querySelector('.btn-spinner')?.classList.toggle('d-none', !loading);
        };

        form.addEventListener('submit', async (e) => {
        e.preventDefault();
        clearAlert();
        clearFieldErrors();
        setLoading(true);

        try {
            const fd = new FormData(form);

            const res = await fetch(ENDPOINT_REGISTER || form.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: fd // no seteas Content-Type manualmente
            });

            if (res.ok) {
            // esperamos {ok:true, redirect:"..."} desde el controlador
            const data = await res.json().catch(() => ({}));
            const redirect = (data && data.redirect) ? data.redirect : (window.REDIRECT_HOME || '/');
            window.location.assign(redirect);
            return;
            }

            if (res.status === 422) {
            const payload = await res.json().catch(() => ({}));
            const errors = payload?.errors || {};
            let first = null;
            Object.keys(errors).forEach(f => {
                const msg = Array.isArray(errors[f]) ? errors[f][0] : String(errors[f]);
                first ??= msg;
                setFieldError(f, msg);
            });
            setAlert('danger', first || 'Revisa los campos resaltados.');
            return;
            }

            // Otros errores
            const text = await res.text();
            console.error('Register error', res.status, text);
            setAlert('danger', 'Ocurrió un error al registrar. Intenta nuevamente.');

        } catch (err) {
            console.error(err);
            setAlert('danger', 'No se pudo conectar con el servidor.');
        } finally {
            setLoading(false);
        }
        });
    }
  

    

    cambia_posiciones() {

        console.log(BASEURL);

        const _this = this;
        $('#departamento').on('change', function() { 

            console.log('post');

            var departamento = $( "#departamento" ).val();  
            var obj_div = document.getElementById('DivResultado_posiciones'); 
            var selec1 = '<div class="input-group mb-3">';
            selec1 += '<label style="width: 130px;" class="input-group-text" for="posiciones">Posicion</label>';
            selec1 += '<select class="form-select" id="posiciones" name="posiciones">';
            selec1 += '<option value="" selected disabled>Selecciona...</option>';
            var selec2 = '</select> </div>';
            var valor = selec1;
                
            if(departamento != ''){
                $.post( BASEURL+'/buscaposiciones', 
                    {
                    _token:token, departamento:departamento
                    }
                    ).done(function( data ) {
                        if(data.response == true){ 
                        $.each(data.data, function (id, value) {
                            $.each(value, function (id, valur) {
                                const posiciones = valur;
                                valor +=posiciones;
                            });
                        });
                        
                        valor +=selec2;
                        obj_div.innerHTML =valor;

                        console.log(valor);
                        //_this.cambia_corregimiento();
                    }
                    })
                    .fail(function() {
                    })
                    .always(function() {
                    }, "json");
                //console.log(provincia, Authrito, corregimiento);
            }else{

                console.log('no post');


                const posiciones = '<option value="" seleted >S/A</option>';            
                valor +=posiciones;            
                valor +=selec2;
                obj_div.innerHTML =valor;
            // _this.cambia_corregimiento();
            }

        });    

    }

      


  }


$(document).ready(function(){
    const objAuthregistro = new Authregistro();
    objAuthregistro.init();
});