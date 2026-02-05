class Payment {

    constructor() {
        this.token = null;
    }

    init() {

        if (!$('#creditcard-container').length) return;

        this.loadWidgetScript();
        this.acciones();
    }

    // ======================================================
    // Cargar script REAL del Widget (NO AJAX)
    // ======================================================

    loadWidgetScript() {

        const url = window.paymentConfig.testMode
            ? window.paymentConfig.widgetUrls.test
            : window.paymentConfig.widgetUrls.prod;

        const script = document.createElement('script');
        script.src = url;

        script.onload = () => {
            this.initWidget();
        };

        document.body.appendChild(script);
    }

    // ======================================================
    // Inicializar Widget
    // ======================================================

    initWidget() {

        if (typeof NeoPaymentWidget === 'undefined') {
            console.error('NeoPaymentWidget no cargó aún...');
            return;
        }

        NeoPaymentWidget.init({
            apiKey: window.paymentConfig.apiKey,
            container: 'creditcard-container',

            onSuccess: (response) => {

                console.log('TOKEN generado:', response);

                this.token = response.token;

                $('#token-input').val(this.token);
                $('#btn-process').prop('disabled', false);
                $('#widget-loading').remove();
            },

            onError: (err) => {
                console.error(err);
                alert('Error validando la tarjeta.');
            }
        });
    }

    // ======================================================
    // Eventos
    // ======================================================

    acciones() {

        $('#process-payment-form').off('submit').on('submit', (e) => {
            this.procesarPago(e);
        });
    }

    // ======================================================
    // Procesar pago en Laravel
    // ======================================================

    async procesarPago(e) {

        e.preventDefault();

        if (!this.token) {
            alert('Debe completar la tarjeta.');
            return;
        }

        const btn = $('#btn-process');
        btn.prop('disabled', true).text('Procesando...');

        try {

            const response = await fetch(window.paymentConfig.routes.process, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.paymentConfig.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    token: this.token,
                    solicitud_id: window.paymentConfig.solicitudId,
                    amount: window.paymentConfig.amount,
                    currency: 'USD'
                })
            });

            const result = await response.json();

            if (result.success) {
                window.location.href = window.paymentConfig.routes.success;
            } else {
                btn.prop('disabled', false).text('Procesar Pago');
                alert(result.message || 'Error procesando el pago.');
            }

        } catch (error) {
            console.error(error);
            btn.prop('disabled', false).text('Procesar Pago');
            alert('Error de conexión con el servidor.');
        }
    }
}

// ======================================================
// Inicialización estilo SNM
// ======================================================

$(document).ready(function () {
    new Payment().init();
});
