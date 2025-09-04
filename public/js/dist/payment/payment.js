// public/js/dist/payment/payment.js

class PaymentWidget {
    constructor() {
      console.log('🔄 Instanciando PaymentWidget...');
      this.cardToken = null;        // GUID (AccountToken)
      this.accountNumber = null;    // W-...
      this.cardholderName = null;
      this.associatedCard = null;   // PAN enmascarado
      this.cardExpiry = null;
      this.cardBrand = null;
      this.cvv = null;
  
      // control interno
      this._tok = null;               // promesa de tokenización en curso
      this._lastDeclinedToken = null; // para forzar re-tokenización si fue rechazada
  
      this.init();
    }
  
    init() {
      this.setupEventListeners();
      this.loadWidget(); // carga widget vía AJAX
  
      // Resumen (monto/moneda)
      const amt = document.querySelector('input[name="amount"]');
      const cur = document.querySelector('select[name="currency"]');
      const $outAmt = document.getElementById('summary-amount');
      const $outCur = document.getElementById('summary-currency');
  
      const fmt = (n) => {
        const v = parseFloat(n || 0);
        return (isNaN(v) ? 0 : v).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      };
      const syncSummary = () => {
        if ($outAmt) $outAmt.textContent = `$${fmt(amt?.value)}`;
        if ($outCur) $outCur.textContent = (cur?.value === '840' ? 'USD' : (cur?.value || ''));
      };
      amt?.addEventListener('input', syncSummary);
      cur?.addEventListener('change', syncSummary);
      syncSummary();
    }
  
    // Marca detectada por PAN enmascarado (fallback)
    getBrandFromMaskedPan(panMasked) {
      if (!panMasked) return '';
      const digits = panMasked.replace(/\D/g, '');
      const first1 = digits.slice(0, 1);
      const first2 = parseInt(digits.slice(0, 2) || '0', 10);
      const first6 = parseInt(digits.slice(0, 6) || '0', 10);
      if (first1 === '4') return 'VISA';
      if ((first2 >= 51 && first2 <= 55) || (first6 >= 222100 && first6 <= 272099)) return 'MASTERCARD';
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
        data: { APIKey: window.paymentConfig.apiKey, Culture: "es" },
        dataType: "html",
        success: (htmlResponse) => {
          const html = (typeof htmlResponse === "object" && htmlResponse.Html) ? htmlResponse.Html : htmlResponse;
          $("#creditcard-container").html(html);
          console.log("✅ Widget cargado correctamente");
  
          // Ajustes ligeros
          this.customizeWidgetUI();
          this.hideWidgetMessages();
          this.hideWidgetSaveButton();   // 👈 oculta el botón del widget (pero clickeable)
        },
        error: (xhr) => {
          console.error("❌ Error cargando widget:", xhr.responseText);
          $("#creditcard-container").html("<div class='alert alert-danger'>No se pudo cargar el formulario de tarjeta</div>");
        }
      });
    }
  
    // Cambia “Guardar”→“Continuar” y oculta “Cancelar”
    customizeWidgetUI() {
      try {
        const root = document.getElementById('creditcard-container');
        if (!root) return;
        const tweak = () => {
          const els = root.querySelectorAll('button, input[type="button"], input[type="submit"]');
          els.forEach((el) => {
            const isInput = el.tagName === 'INPUT';
            const raw = isInput ? (el.value || '') : (el.textContent || '');
            const txt = raw.trim().toLowerCase();
            if (txt === 'guardar') { isInput ? (el.value = 'Continuar') : (el.textContent = 'Continuar'); }
            if (txt === 'cancelar') el.style.display = 'none';
          });
        };
        tweak(); setTimeout(tweak, 500);
      } catch (e) { console.error('customizeWidgetUI error:', e); }
    }
  
    // Oculta banners/mensajes del proveedor (no toca el form)
    hideWidgetMessages() {
      try {
        const root = document.getElementById('creditcard-container');
        if (!root) return;
        const selectors = [
          '#tokenResponse', '#savecreditcardresponse', '#divTokenResponse',
          '.token-response', '.save-creditcard-response',
          '.card-ui-component-message', '.card-ui-component-alert',
          '[id*="TokenResponse"]', '[class*="token-response"]'
        ];
        selectors.forEach(sel => {
          root.querySelectorAll(sel).forEach(el => {
            if (el.querySelector('input,select,textarea,button')) return;
            el.style.setProperty('display', 'none', 'important');
          });
        });
      } catch (e) { console.warn('hideWidgetMessages error:', e); }
    }

    hideWidgetTokenResponse() {
        try {
          const root = document.getElementById('creditcard-container');
          if (!root) return;
      
          const hideMatches = () => {
            // 1) Si el proveedor usa IDs/clases "obvias", intenta ocultarlas primero
            const quickSelectors = [
              '#tokenResponse', '.token-response', '#savecreditcardresponse',
              '.save-creditcard-response', '.card-ui-component-token-response',
              '.card-ui-component-message', '.card-ui-component-alert'
            ];
            quickSelectors.forEach(sel => {
              root.querySelectorAll(sel).forEach(el => el.style.display = 'none');
            });
      
            // 2) Filtro por texto (robusto a cambios de markup)
            const textNeedle = /(la información de la tarjeta.*guardada|token response|account token|éxito)/i;
            const nodes = root.querySelectorAll('div,section,article,aside,p,span,li,h1,h2,h3,h4,h5,h6');
            nodes.forEach(el => {
              const t = (el.textContent || '').trim();
              if (t && textNeedle.test(t)) {
                // Oculta el contenedor inmediato que suele agrupar el mensaje
                (el.closest('.card-ui-component-outer') || el).style.display = 'none';
              }
            });
          };
      
          // Ejecuta ahora y reintenta por si aparece con delay
          hideMatches();
          setTimeout(hideMatches, 50);
          setTimeout(hideMatches, 300);
          setTimeout(hideMatches, 1000);
      
          // Observa futuros cambios del widget
          if ('MutationObserver' in window) {
            const obs = new MutationObserver(() => hideMatches());
            obs.observe(root, { childList: true, subtree: true });
            // guarda por si quieres desconectarlo más tarde
            this._tokenMsgObserver = obs;
          }
        } catch (err) {
          console.warn('hideWidgetTokenResponse error:', err);
        }
      }
  
    // 🔒 oculta el botón del widget pero sigue siendo “clickeable”
    hideWidgetSaveButton() {
      const btn = this.getWidgetSaveButton();
      if (!btn) return;
      btn.style.position = 'absolute';
      btn.style.left = '-9999px';
      btn.style.width = '1px';
      btn.style.height = '1px';
      btn.style.overflow = 'hidden';
      btn.disabled = false; // por si el widget lo deshabilita
    }
  
    setupEventListeners() {
      $('#process-payment-form').on('submit', (e) => this.handlePaymentSubmit(e));
      this.addCVVField();
      // Si el usuario edita cualquier campo del widget → invalidar token para forzar re-tokenización
      const root = document.getElementById('creditcard-container');
      if (root) {
        root.addEventListener('input', () => { this.cardToken = null; }, true);
        root.addEventListener('change', () => { this.cardToken = null; }, true);
      }
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
  
    // === Tokenización (1 clic) =================================================
  
    getWidgetSaveButton() {
      const root = document.getElementById('creditcard-container');
      if (!root) return null;
      return (
        root.querySelector('.card-ui-component-save-btn') ||
        Array.from(root.querySelectorAll('button, input[type="button"], input[type="submit"]'))
          .find(el => {
            const t = (el.tagName === 'INPUT' ? el.value : el.textContent).trim().toLowerCase();
            return t === 'guardar' || t === 'continuar';
          }) ||
        null
      );
    }
  
    clickWidgetSave(btn) {
      // 1) intenta fn global del proveedor
      try { if (typeof window.SaveCreditCard === 'function') return window.SaveCreditCard(); } catch {}
      // 2) dispara eventos de click como humano
      try {
        btn.removeAttribute('disabled'); btn.disabled = false;
        ['pointerdown','mousedown','mouseup','click'].forEach(type =>
          btn.dispatchEvent(new MouseEvent(type, { bubbles: true, cancelable: true, view: window }))
        );
      } catch {}
    }
  
    async ensureTokenized(timeoutMs = 25000) {
      // Si ya hay token GUID y no es uno previamente rechazado
      if (this.cardToken && this.isGuid(this.cardToken) && this.cardToken !== this._lastDeclinedToken) return true;
  
      // limpiar token previo
      this.cardToken = null;
  
      const btn = this.getWidgetSaveButton();
      if (!btn) {
        this.showMessage("No se encontró el botón del formulario de tarjeta. Recargando…", "danger");
        this.loadWidget();
        throw new Error('no_widget_button');
      }
  
      // promesa controlada
      let resolveTok, rejectTok;
      const promise = new Promise((res, rej) => { resolveTok = res; rejectTok = rej; });
      const timer = setTimeout(() => {
        this._tok = null;
        rejectTok(new Error('timeout tokenization'));
      }, timeoutMs);
  
      this._tok = {
        resolve: () => { clearTimeout(timer); this._tok = null; resolveTok(true); },
        reject:  (err) => { clearTimeout(timer); this._tok = null; rejectTok(err || new Error('tokenization failed')); }
      };
  
      // ocultar banners y disparar click
      this.hideWidgetMessages();
      this.clickWidgetSave(btn);
  
      return promise;
    }
  
    // Callback del widget al tokenizar OK
    handleTokenizationSuccess(response) {
      const td = (response && (response.TokenDetails || response.tokenDetails)) || response || {};
      if (!(td && (td.AccountToken || td.Token))) {
        this.showMessage("Error: no se recibió token válido del proveedor.", "danger");
        this._tok?.reject?.(new Error('no_token'));
        return;
      }
  
      console.log("🎉 Tarjeta tokenizada:", response);
  
      this.cardToken      = td.AccountToken || td.Token || null;
      this.accountNumber  = td.AccountNumber || "";
      this.cardholderName = td.CardHolderName || td.CardholderName || "";
      this.associatedCard = td.CardNumber || td.MaskedPan || "";
      this.cardExpiry     = td.ExpirationDate || td.Expiry || "";
      this.cardBrand      = td.Brand || td.CardBrand || td.CardType || this.getBrandFromMaskedPan(this.associatedCard);
  
      $('#token-input').val(this.cardToken);
      $('#account-number-input').val(this.accountNumber);
  
      this.hideWidgetMessages();
      this._tok?.resolve?.(true);
    }
  
    // === Submit general (1 clic) ===============================================
  
    async handlePaymentSubmit(e) {
      e.preventDefault();
      if (this._submitting) return;
      this._submitting = true;
  
      const btn = document.getElementById("btn-process");
      const originalTxt = btn.innerHTML;
      btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Validando tarjeta...';
      btn.disabled = true;
  
      try {
        // 1) Tokenizar si hace falta (clickeando el botón oculto del widget)
        await this.ensureTokenized();
  
        // 2) Validar CVV y datos
        this.cvv = ($('#cvv-input').val() || '').replace(/\D/g, '');
        if (!/^\d{3,4}$/.test(this.cvv)) throw new Error('Ingrese un CVV válido (3 o 4 dígitos).');
        if (!this.validateAdditionalFields()) throw new Error('Campos incompletos.');
  
        // 3) Guardar token en tu backend
        const tokenResult = await this.saveTokenToServer();
        if (!tokenResult?.success) throw new Error(tokenResult?.message || 'No se pudo guardar la tarjeta.');
  
        // 4) Cobrar
        btn.innerHTML = originalTxt; // processPayment pondrá su propio spinner
        await this.processPayment();
  
      } catch (err) {
        console.error('handlePaymentSubmit error:', err);
        const msg = (err && err.message) ? err.message : 'No fue posible validar la tarjeta. Verifica los datos.';
        this.showMessage(msg, 'danger');
        btn.innerHTML = originalTxt;
        btn.disabled = false;
      } finally {
        this._submitting = false;
      }
    }
  
    validateAdditionalFields() {
      const ruex = document.querySelector('input[name="ruex"]').value.trim();
      const fullName = document.querySelector('input[name="full_name"]').value.trim();
      const email = document.querySelector('input[name="email"]').value.trim();
      const amount = document.querySelector('input[name="amount"]').value.trim();
  
      if (!ruex)   return this.showMessage("El campo RUEX es requerido", "warning"), false;
      if (!fullName) return this.showMessage("El nombre completo es requerido", "warning"), false;
      if (!email)  return this.showMessage("El correo electrónico es requerido", "warning"), false;
      if (!amount || parseFloat(amount) <= 0) return this.showMessage("El monto debe ser mayor a 0", "warning"), false;
      return true;
    }
  
    async saveTokenToServer() {
      const normalizeExpiry = (raw) => {
        if (!raw) return null;
        const s = String(raw).replace(/\s/g, '');
        const m = s.match(/^(\d{2})[\/\-]?(\d{2,4})$/);
        if (!m) return null;
        const mm = m[1];
        const yyyy = m[2].length === 2 ? ('20' + m[2]) : m[2];
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
            brand: this.cardBrand || "",
            expiry_date: normalizeExpiry(this.cardExpiry)
          })
        });
  
        const text = await response.text();
        let result;
        try { result = JSON.parse(text); } catch { result = { success: false, message: text }; }
  
        if (!response.ok) {
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
        return Object.entries(errors).map(([f, msgs]) => `${f}: ${[].concat(msgs).join(', ')}`).join('<br>');
      } catch { return 'Datos inválidos'; }
    }
  
    async processPayment() {
      try {
        if (!this.isGuid(this.cardToken)) {
          this.showMessage("No se recibió un token válido. Vuelve a guardar la tarjeta.", "danger");
          return;
        }
        if (!/^\d{3,4}$/.test(this.cvv || '')) {
          this.showMessage("CVV inválido. Debe tener 3 o 4 dígitos.", "warning");
          return;
        }
  
        const amount   = document.querySelector('input[name="amount"]')?.value || '';
        const currency = document.querySelector('select[name="currency"]')?.value || 'USD';
  
        const formData = new FormData();
        formData.append("token", this.cardToken);
        formData.append("account_number", this.accountNumber);
        formData.append("cvv", this.cvv);
        formData.append("ruex", document.querySelector('input[name="ruex"]').value);
        formData.append("full_name", document.querySelector('input[name="full_name"]').value);
        formData.append("email", document.querySelector('input[name="email"]').value);
        formData.append("amount", amount);
        formData.append("currency", currency);
        formData.append("_token", window.paymentConfig.csrfToken);
  
        const submitBtn   = document.getElementById("btn-process");
        const originalTxt = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';
        submitBtn.disabled  = true;
  
        const response = await fetch(window.paymentConfig.routes.process, {
          method: "POST",
          body: formData,
          headers: { "X-Requested-With": "XMLHttpRequest", "Accept": "application/json" }
        });
  
        const raw = await response.text();
        let result;
        try { result = JSON.parse(raw); } catch { result = { success: false, message: raw }; }
  
        submitBtn.innerHTML = originalTxt;
        submitBtn.disabled  = false;
  
        if (!response.ok) {
          if (response.status === 422 && result && result.errors) {
            const msg = this.buildValidationMessage(result.errors);
            this.showMessage("Datos inválidos:<br>" + msg, "warning");
            return;
          }
          const paramsHttp = new URLSearchParams({
            message: result.message || `Error HTTP ${response.status}`,
            code:    'HTTP_' + response.status,
            amount, currency
          });
          if (window.paymentConfig.routes.error) {
            window.location.href = `${window.paymentConfig.routes.error}?${paramsHttp.toString()}`;
          } else {
            this.showMessage(result.message || `Error HTTP ${response.status}`, "danger");
          }
          return;
        }
  
        // Rechazo / fallo → ve a /payment/error
        if (!result.success) {
          const r = result.response || {};
          const params = new URLSearchParams({
            message: result.message || r.response_description || r.description || r.error || 'Transacción rechazada',
            code:    r.response_code || r.Code || r.code || '',
            result:  r.result || r.Result || '',
            auth:    r.authorization_number || r.AuthorizationNumber || '',
            tid:     (result.transaction && result.transaction.gateway_transaction_id) || r.transaction_id || r.TransactionId || '',
            ref:     r.tracking || r.system_tracking || '',
            amount, currency
          });
          // marca token como rechazado para re-tokenizar en próximo intento
          this._lastDeclinedToken = this.cardToken;
          this.cardToken = null;
          if (window.paymentConfig.routes.error) {
            window.location.href = `${window.paymentConfig.routes.error}?${params.toString()}`;
          } else {
            this.showMessage("Error en el pago: " + (result.message || 'Rechazado'), "danger");
          }
          return;
        }
  
        // Éxito ✅
        this.showMessage("¡Pago procesado exitosamente! Redirigiendo...", "success");
        setTimeout(() => {
          window.location.href = `${window.paymentConfig.routes.success}?transaction=${result.transaction.id}`;
        }, 600);
  
      } catch (error) {
        console.error("Payment processing error:", error);
        const amount   = document.querySelector('input[name="amount"]')?.value || '';
        const currency = document.querySelector('select[name="currency"]')?.value || 'USD';
        const params   = new URLSearchParams({
          message: "Error de conexión: " + (error?.message || 'desconocido'),
          code: 'NETWORK', amount, currency
        });
        const submitBtn = document.getElementById("btn-process");
        submitBtn.innerHTML = '<i class="fas fa-credit-card me-2"></i>Procesar Pago';
        submitBtn.disabled  = false;
        if (window.paymentConfig.routes.error) {
          window.location.href = `${window.paymentConfig.routes.error}?${params.toString()}`;
        } else {
          this.showMessage("Error de conexión: " + (error?.message || 'desconocido'), "danger");
        }
      }
    }
  
    showMessage(message, type) {
      const msgDiv = $("#widget-messages");
      msgDiv.removeClass().addClass(`alert alert-${type}`).html(message).show();
      msgDiv[0]?.scrollIntoView({ behavior: "smooth", block: "center" });
      setTimeout(() => msgDiv.fadeOut(), 5000);
    }
  }
  
  /* ========= Callbacks globales del widget ========= */
  
  // ÉXITO: setea campos y resuelve promesa de ensureTokenized()
  window.SaveCreditCard_SuccessCallback = function (response) {
    if (window.paymentWidget) {
      window.paymentWidget.handleTokenizationSuccess(response);
      window.paymentWidget.hideWidgetTokenResponse?.();
      window.paymentWidget._tok?.resolve?.(true);
    }
  };
  
  // FALLO: rechaza promesa y muestra mensaje
  window.SaveCreditCard_FailureCallback = function (response) {
    const r = response || {};
    const msg =
      r.ResponseDescription || r.Description || r.ResponseMessage ||
      'No fue posible validar la tarjeta. Verifica número, fecha y CVV, o intenta con otra tarjeta.';
    console.warn("❌ Error en tokenización:", r);
    if (window.paymentWidget) {
      window.paymentWidget._tok?.reject?.(new Error('tokenization_failed'));
      window.paymentWidget.showMessage(msg, "danger");
    }
  };
  
  // CANCEL: fallo controlado
  window.SaveCreditCard_CancelCallback = function () {
    console.warn("⚠️ Tokenización cancelada por el usuario");
    window.paymentWidget?._tok?.reject?.(new Error('cancelled'));
    window.paymentWidget?.showMessage("Tokenización cancelada por el usuario", "warning");
  };
  
  // Inicializar
  document.addEventListener("DOMContentLoaded", function () {
    console.log("📄 Documento cargado, iniciando PaymentWidget...");
    window.paymentWidget = new PaymentWidget();
  });
  