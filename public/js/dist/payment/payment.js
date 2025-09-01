// public/js/dist/payment/payment.js

class PaymentWidget {
    constructor() {
        console.log('🔄 Instanciando PaymentWidget...');
        this.cardToken = null;
        this.accountNumber = null;
        this.cardholderName = null;
        this.associatedCard = null;
        this.cardExpiry = null;
        this.cvv = null;

        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadWidget(); // ✅ carga vía AJAX, no iframe
    }


     getBrandFromMaskedPan(panMasked) {
       if (!panMasked) return '';
       const first6 = panMasked.replace(/\D/g, '').slice(0, 6); // e.g., "5114.."
       const first1 = first6.slice(0,1), first2 = first6.slice(0,2), first4 = first6.slice(0,4);
       const first6num = parseInt(first6 || '0', 10);
       if (first1 === '4') return 'VISA';
       if ((+first2 >= 51 && +first2 <= 55) || (first6num >= 222100 && first6num <= 272099)) return 'MASTERCARD';
       return '';
     }

    

    loadWidget() {
        console.log('🚀 Cargando widget de pago...');
    
        const widgetUrl = window.paymentConfig.testMode
            ? window.paymentConfig.widgetUrls.test
            : window.paymentConfig.widgetUrls.prod;
    
        $.ajax({
            type: "GET",
            url: widgetUrl,
            data: {
                APIKey: window.paymentConfig.apiKey,
                Culture: "es"
            },
            dataType: "html", // 👈 muy importante
            success: function (htmlResponse) {
                //console.log("🔎 Respuesta cruda:", htmlResponse);
    
                // Si htmlResponse es objeto con propiedad Html
                if (typeof htmlResponse === "object" && htmlResponse.Html) {
                    $("#creditcard-container").html(htmlResponse.Html);
                } else {
                    // Si ya es HTML plano
                    $("#creditcard-container").html(htmlResponse);
                }
    
                console.log("✅ Widget cargado correctamente");
            },
            error: function (xhr, status, error) {
                console.error("❌ Error cargando widget:", xhr.responseText);
                $("#creditcard-container").html(
                    "<div class='alert alert-danger'>No se pudo cargar el formulario de tarjeta</div>"
                );
            }
        });
    }
    

    setupEventListeners() {
        $('#process-payment-form').on('submit', (e) => this.handlePaymentSubmit(e));
        this.addCVVField();
    }

    addCVVField() {
        const cvvHtml = `
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">CVV <small>(3 dígitos atrás de la tarjeta)</small></label>
                    <input type="text" class="form-control" name="cvv" id="cvv-input" 
                            maxlength="4" placeholder="123" required inputmode="numeric" pattern="\\d{3,4}">
                </div>
            </div>
        `;
        $('#process-payment-form').prepend(cvvHtml);
    }

    // ✅ Callback de éxito (llamado por el widget automáticamente)
    handleTokenizationSuccess(response) {
        console.log("🎉 Tarjeta tokenizada:", response);

        const td = response.TokenDetails || response.tokenDetails || response; // tolerante
      
        if (!td || !(td.AccountToken || td.Token)) {
            this.showMessage("Error: No se recibió token válido", "danger");
            return;
        }
        //this.cardToken      = td.AccountToken || td.Token;
        //this.accountNumber  = td.AccountNumber || td.MaskedAccount || "";
        this.cardToken     = td.AccountToken || td.Token || null; // NO usar AccountNumber como token
        this.accountNumber = td.AccountNumber || "";
        this.cardholderName = td.CardHolderName || td.CardholderName || "";
        this.associatedCard = td.CardNumber || td.MaskedPan || "";
        this.cardExpiry     = td.ExpirationDate || td.Expiry || "";  // suele venir MM/YY o MM/YYYY
        //this.cardBrand      = td.Brand || td.CardBrand || td.Scheme || ""; // NUEVO
        this.cardBrand      = td.Brand || td.CardBrand || td.CardType || this.getBrandFromMaskedPan(this.associatedCard);


        // Guardar en inputs ocultos
        $('#token-input').val(this.cardToken);
        $('#account-number-input').val(this.accountNumber);

        this.showMessage("¡Tarjeta validada correctamente! Ahora ingrese el CVV para procesar.", "success");
        this.displayCardInfo();
        this.scrollToPaymentForm();
    }

    displayCardInfo() {
        if (!this.cardholderName || !this.associatedCard) return;

        const cardInfoHtml = `
            <div class="alert alert-success mt-3">
                <h6>💳 Tarjeta Guardada Exitosamente</h6>
                <p><strong>Tarjetahabiente:</strong> ${this.cardholderName}</p>
                <p><strong>Terminación:</strong> **** ${this.associatedCard.slice(-4)}</p>
                <p><strong>Token:</strong> ${this.cardToken.substring(0, 8)}... (oculto por seguridad)</p>
            </div>
        `;
        $('#widget-messages').html(cardInfoHtml).show();
    }

    async handlePaymentSubmit(e) {
        e.preventDefault();

        console.log("🖱️ Botón de pago clickeado");
        console.log("🔐 Token actual:", this.cardToken);

        if (!this.cardToken) {
            this.showMessage("Debe ingresar y validar la tarjeta primero.", "warning");
            return;
        }

        this.cvv = $('#cvv-input').val();
        if (!this.cvv || this.cvv.length < 3) {
            this.showMessage("Ingrese el código CVV de seguridad.", "warning");
            return;
        }

        if (!this.validateAdditionalFields()) return;

        const tokenResult = await this.saveTokenToServer();
        if (!tokenResult.success) return;

        await this.processPayment();
    }

    validateAdditionalFields() {
        const ruex = document.querySelector('input[name="ruex"]').value.trim();
        const fullName = document.querySelector('input[name="full_name"]').value.trim();
        const email = document.querySelector('input[name="email"]').value.trim();
        const amount = document.querySelector('input[name="amount"]').value.trim();

        if (!ruex) return this.showMessage("El campo RUEX es requerido", "warning"), false;
        if (!fullName) return this.showMessage("El nombre completo es requerido", "warning"), false;
        if (!email) return this.showMessage("El correo electrónico es requerido", "warning"), false;
        if (!amount || parseFloat(amount) <= 0) return this.showMessage("El monto debe ser mayor a 0", "warning"), false;

        return true;
    }

    async saveTokenToServer() {

        const normalizeExpiry = (raw) => {
            if (!raw) return null;
            const s = String(raw).replace(/\s/g,'');
            const m = s.match(/^(\d{2})[\/\-]?(\d{2,4})$/);
            if (!m) return null;
            const mm = m[1];
            const yyyy = m[2].length === 2 ? ('20'+m[2]) : m[2];
            return `${yyyy}-${mm}`;
            };
        try {
            const response = await fetch(window.paymentConfig.routes.handleWidgetCallback, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": window.paymentConfig.csrfToken,
                    "X-Requested-With": "XMLHttpRequest",
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    token: this.cardToken,
                    account_number: this.accountNumber,
                    cardholder_name: this.cardholderName,
                    last_four: this.associatedCard ? this.associatedCard.slice(-4) : "",
                    //expiry_date: this.cardExpiry
                    //brand: this.cardBrand || "",
                   // expiry_date: normalizeExpiry(this.cardExpiry)
                    brand: this.cardBrand || "",
                    expiry_date: normalizeExpiry(this.cardExpiry) // puede quedar null si no venía
                })
            });

            // const result = await response.json();
            // if (!result.success) {
            //     this.showMessage("Error al guardar la tarjeta: " + result.message, "danger");
            // }
            // return result;

        const text = await response.text(); // lee como texto para capturar también errores HTML
          let result;
           try { result = JSON.parse(text); } catch { result = { success: false, message: text }; }
           if (!response.ok) {
             // Mostrar errores de validación si los hay
             const errors = (result && result.errors) ? JSON.stringify(result.errors) : result.message || 'Error';
             this.showMessage("Error al guardar la tarjeta: " + errors, "danger");
             return { success: false };
           }
           if (!result.success) {
             this.showMessage("Error al guardar la tarjeta: " + (result.message || 'Error'), "danger");
           }
           return result;

        } catch (error) {
            console.error("Error saving token:", error);
            this.showMessage("Error de conexión al guardar la tarjeta", "danger");
            return { success: false };
        }
    }
    isGuid(v) {
        return /^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i.test(v || '');
      }
      
    buildValidationMessage(errors) {
        if (!errors) return 'Datos inválidos';
        try {
          return Object.entries(errors)
            .map(([field, msgs]) => `${field}: ${[].concat(msgs).join(', ')}`)
            .join('<br>');
        } catch { return 'Datos inválidos'; }
      }

    async processPayment() {
        try {
          // Guards rápidos (opcional pero recomendado)
          if (!this.isGuid(this.cardToken)) {
            this.showMessage("No se recibió un token válido. Vuelve a guardar la tarjeta.", "danger");
            return;
          }
          if (!/^\d{3,4}$/.test(this.cvv || '')) {
            this.showMessage("CVV inválido. Debe tener 3 o 4 dígitos.", "warning");
            return;
          }
      
          const formData = new FormData();
          formData.append("token", this.cardToken);               // GUID (AccountToken)
          formData.append("account_number", this.accountNumber);  // W-... (por si lo usas en backend)
          formData.append("cvv", this.cvv);
          formData.append("ruex", document.querySelector('input[name="ruex"]').value);
          formData.append("full_name", document.querySelector('input[name="full_name"]').value);
          formData.append("email", document.querySelector('input[name="email"]').value);
          formData.append("amount", document.querySelector('input[name="amount"]').value);
          formData.append("currency", document.querySelector('select[name="currency"]').value);
          formData.append("_token", window.paymentConfig.csrfToken);
      
          const submitBtn = document.getElementById("btn-process");
          const originalText = submitBtn.innerHTML;
          submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';
          submitBtn.disabled = true;
      
          const response = await fetch(window.paymentConfig.routes.process, {
            method: "POST",
            body: formData,
            headers: {
              "X-Requested-With": "XMLHttpRequest",
              "Accept": "application/json"
            }
          });
      
          // Lee como texto por si llega HTML (p.ej., redirección a login)
          const raw = await response.text();
          let result;
          try { result = JSON.parse(raw); } catch { result = { success: false, message: raw }; }
      
          // Restablece botón
          submitBtn.innerHTML = originalText;
          submitBtn.disabled = false;
      
          // Manejo de errores HTTP (422 validación, 500, etc.)
          if (!response.ok) {
            if (response.status === 422 && result && result.errors) {
              const msg = this.buildValidationMessage(result.errors);
              this.showMessage("Datos inválidos:<br>" + msg, "warning");
              return;
            }
            this.showMessage(result.message || `Error HTTP ${response.status}`, "danger");
            return;
          }
      
          // Manejo de rechazo de pasarela
          if (!result.success) {
            console.log('❌ Gateway response:', result.response || result);
            const msg =
              result.message ||
              (result.response && (result.response.response_description || result.response.description || result.response.error)) ||
              'Error de la pasarela';
            this.showMessage("Error en el pago: " + msg, "danger");
            return;
          }
      
          // Éxito
          this.showMessage("¡Pago procesado exitosamente! Redirigiendo...", "success");
          setTimeout(() => {
            window.location.href = `${window.paymentConfig.routes.success}?transaction=${result.transaction.id}`;
          }, 2000);
      
        } catch (error) {
          console.error("Payment processing error:", error);
          this.showMessage("Error de conexión: " + error.message, "danger");
      
          const submitBtn = document.getElementById("btn-process");
          submitBtn.innerHTML = '<i class="fas fa-credit-card me-2"></i>Procesar Pago';
          submitBtn.disabled = false;
        }
      }

    showMessage(message, type) {
        const msgDiv = $("#widget-messages");
        msgDiv.removeClass().addClass(`alert alert-${type}`).html(message).show();
        msgDiv[0].scrollIntoView({ behavior: "smooth", block: "center" });
        setTimeout(() => msgDiv.fadeOut(), 5000);
    }

    scrollToPaymentForm() {
        const paymentForm = document.getElementById("payment-form");
        if (paymentForm) {
            paymentForm.scrollIntoView({ behavior: "smooth", block: "center" });
        }
    }
}

    // ✅ Definir los callbacks globales que el widget llama
    window.SaveCreditCard_SuccessCallback = function (response) {
        if (window.paymentWidget) {
            window.paymentWidget.handleTokenizationSuccess(response);
        }
    };

    window.SaveCreditCard_FailureCallback = function (response) {
        console.error("❌ Error en tokenización:", response);
        if (window.paymentWidget) {
            window.paymentWidget.showMessage("Error en tokenización: " + response.ResponseDescription, "danger");
        }
    };

    window.SaveCreditCard_CancelCallback = function () {
        console.warn("⚠️ Tokenización cancelada por el usuario");
        if (window.paymentWidget) {
            window.paymentWidget.showMessage("Tokenización cancelada por el usuario", "warning");
        }
    };

    // Inicializar
    document.addEventListener("DOMContentLoaded", function () {
        console.log("📄 Documento cargado, iniciando PaymentWidget...");
        window.paymentWidget = new PaymentWidget();
    });
