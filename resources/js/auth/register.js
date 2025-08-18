    // resources/js/auth/register.js

    // Si usas jQuery en otras partes, asegúrate de que window.$ exista (lo configuramos en resources/js/app.js)
    const $jq = window.jQuery || window.$ || null;

    // Helper: tomar token CSRF desde el <meta> del layout
    function getCsrf() {
    const t = document.querySelector('meta[name="csrf-token"]');
    return t ? t.getAttribute('content') : '';
    }

    // Helper: crear (si no existe) un alert arriba del form para mensajes AJAX
    function ensureAjaxAlert(form) {
    let box = form.querySelector('#ajaxAlert');
    if (!box) {
        box = document.createElement('div');
        box.id = 'ajaxAlert';
        box.className = 'alert d-none';
        form.prepend(box);
    }
    return box;
    }

class Authregistro {

    constructor() {
        
    }

    init() {

        const form = document.getElementById('registerForm');
        if (!form) return;

        // UI bindings
        this.bindTogglePassword();
        this.bindCapsLockHint();
        this.bindUserTypeToggle();
        this.bindIdoneidadValidation();

        this.bindFiliacionLookup(); 

        // Envío por AJAX (si quieres activarlo)
        this.bindFormAjax();
        
    }

    bindFiliacionLookup() {
        const btn = document.getElementById('btnValidarFiliacion');
        if (!btn || typeof ENDPOINT_FILIACION === 'undefined') return;

        btn.addEventListener('click', async () => {
        const tipo   = document.getElementById('documento_tipo')?.value;
        const numero = document.getElementById('documento_numero')?.value?.trim();
        const form   = document.getElementById('registerForm');
        const alert  = (form && form.querySelector('#ajaxAlert')) || (() => {
            const a = document.createElement('div');
            a.id = 'ajaxAlert';
            a.className = 'alert d-none';
            form.prepend(a);
            return a;
        })();

        const setAlert = (type, msg) => {
            alert.className = 'alert alert-' + type;
            alert.textContent = msg;
            alert.classList.remove('d-none');
        };
        const clearAlert = () => { alert.className = 'alert d-none'; alert.textContent = ''; };

        clearAlert();

        if (tipo !== 'Ruex' || !numero) {
            setAlert('warning','Selecciona "N° Filiación" y escribe el número.');
            return;
        }

        btn.disabled = true;

        try {
            const res = await fetch(ENDPOINT_FILIACION, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrf(),
            },
            body: JSON.stringify({ documento_tipo: tipo, documento_numero: numero }),
            });

            if (res.ok) {
            const payload = await res.json();
            if (payload?.ok && payload?.data) {
                const d = payload.data;

                // Autocompleta si hay datos
                const map = {
                primer_nombre: 'primer_nombre',
                segundo_nombre: 'segundo_nombre',
                primer_apellido: 'primer_apellido',
                segundo_apellido: 'segundo_apellido',
                genero: 'genero',
                fecha_nacimiento: 'fecha_nacimiento'
                };
                Object.keys(map).forEach(k => {
                const id = map[k];
                const el = document.getElementById(id);
                if (el && d[k]) el.value = d[k];
                });

                setAlert('success','Filiación encontrada y datos autocompletados.');
            } else {
                setAlert('danger','No se pudo obtener la filiación.');
            }
            } else if (res.status === 422) {
            const j = await res.json().catch(() => ({}));
            const first = j?.message || 'Dato de filiación inválido.';
            setAlert('danger', first);
            } else {
            setAlert('danger','Error al consultar la filiación.');
            }
        } catch (e) {
            console.error(e);
            setAlert('danger','No se pudo conectar con el servidor.');
        } finally {
            btn.disabled = false;
        }
        });
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
        ['keyup', 'keydown', 'focus'].forEach(ev => pwd.addEventListener(ev, update));
        pwd.addEventListener('blur', () => (hint.style.display = 'none'));
    }

    /* ====== UI: Toggle secciones + required dinámicos ====== */
    bindUserTypeToggle() {
        const radios   = document.querySelectorAll('input[name="tipo_usuario"]');
        const secSolic = document.getElementById('sectionSolicitante');
        const secAbog  = document.getElementById('sectionAbogado');
        if (!radios.length || !secSolic || !secAbog) return;

        const camposSolicReq = ['documento_tipo','documento_numero','genero','fecha_nacimiento'];
        const camposAbogReq  = ['abogado_id', 'idoneidad', 'firma_estudio'];

        const setRequired = (ids, value) => {
            ids.forEach(id => {
                const el = document.getElementById(id);
                if (!el) return;
                value ? el.setAttribute('required', 'required') : el.removeAttribute('required');
            });
        };

        const setDisabled = (ids, disabled) => {
            ids.forEach(id => {
                const el = document.getElementById(id);
                if (!el) return;
                el.disabled = disabled;
                if (disabled) {
                if (el.type === 'file') el.value = '';
                // limpiar errores visibles
                el.classList.remove('is-invalid');
                const fb = el.closest('.input-group, .col-12, .mb-3, .form-group')
                            ?.querySelector('.invalid-feedback[data-js="1"]');
                fb && fb.remove();
                }
            });
        };

        const paint = () => {
            const val = document.querySelector('input[name="tipo_usuario"]:checked')?.value || 'solicitante';
            const isAbogado = val === 'abogado';
            secAbog.style.display  = isAbogado ? 'block' : 'none';
            secSolic.style.display = isAbogado ? 'none'  : 'block';
            setRequired(camposAbogReq,  isAbogado);
            setRequired(camposSolicReq, !isAbogado);

            setDisabled(camposAbogReq,   !isAbogado);
            setDisabled(camposSolicReq,   isAbogado);
        };

        radios.forEach(r => r.addEventListener('change', paint));
        paint();
        
    }

    /* ====== UI: Validación de archivo idoneidad (tipo/tamaño) ====== */
    bindIdoneidadValidation() {
        const input = document.getElementById('idoneidad');
        if (!input) return;

        const max   = parseInt(input.dataset.max || 5242880, 10); // 5MB
        const valid = ['application/pdf', 'image/jpeg', 'image/png'];
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

    /* ====== Envío por AJAX (fetch) ====== */
    bindFormAjax() {
        const form = document.getElementById('registerForm');
        const btn  = document.getElementById('btnRegister');
        if (!form) return;

        const alertBox = ensureAjaxAlert(form);
        const token = getCsrf();

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
        if ($jq) {
            $jq('#registerForm .is-invalid').removeClass('is-invalid');
            $jq('#registerForm .invalid-feedback[data-js="1"]').remove();
        } else {
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            form.querySelectorAll('.invalid-feedback[data-js="1"]').forEach(el => el.remove());
        }
        };

        const setFieldError = (field, message) => {
        // Busca el input por name
        const input = form.querySelector(`[name="${CSS.escape(field)}"]`);
        if (!input) return;

        input.classList.add('is-invalid');

        // Busca feedback existente cercano; si no, lo crea
        let fb =
            input.closest('.input-group, .col-12, .mb-3, .form-group')?.querySelector('.invalid-feedback');
        if (!fb) {
            fb = document.createElement('div');
            fb.className = 'invalid-feedback';
            fb.setAttribute('data-js', '1');
            (input.closest('.input-group, .col-12, .mb-3, .form-group') || input.parentElement)
            .appendChild(fb);
        }
        fb.textContent = message;
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

            const res = await fetch(form.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: fd, // No seteamos Content-Type manualmente
            redirect: 'follow',
            });

            // Éxito: puede devolver JSON o redirigir (HTML). Maneja ambos casos.
            if (res.ok) {
            const ctype = res.headers.get('content-type') || '';
            if (ctype.includes('application/json')) {
                const data = await res.json().catch(() => ({}));
                const redirect = (data && data.redirect)
                ? data.redirect
                : (window.REDIRECT_HOME || '/');
                window.location.assign(redirect);
            } else {
                // Si no es JSON, asumimos que Laravel redirigió a otra URL o devolvió HTML.
                // Simplemente enviamos al home (o a la URL actual para refrescar).
                window.location.assign(window.REDIRECT_HOME || '/');
            }
            return;
            }

            // 422 Unprocessable Entity: errores de validación
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
}

    // Inicializar cuando el DOM está listo
document.addEventListener('DOMContentLoaded', () => {
    const objAuthregistro = new Authregistro();
    objAuthregistro.init();
});
