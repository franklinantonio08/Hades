@extends('layouts.admin')

@section('title', 'Método de Pago')

@section('content')
<div class="payment-page">

  <!-- ====== ESTILOS LOCALES (puedes moverlos a app.css) ====== -->
<link href="{{ asset('css/pagos.css') }}" rel="stylesheet">

  <div class="pay-container"><!-- ancho contenido -->

    {{-- HEADER --}}
    <header class="pay-header">
      <img src="{{ asset('images/LOGOconBorde200x229.png') }}" alt="Migración" class="brand">
      <div class="titles">
        <h1>Método de Pago</h1>
        <p>Procese su pago con tarjeta de crédito de forma segura.</p>
      </div>
    </header>

    <div class="row g-4 align-items-start mt-2">
      {{-- IZQUIERDA --}}
      <div class="col-12 col-lg-8 col-xl-8">
        <div class="panel">

          {{-- alertas --}}
          <div class="mb-2">
            @include('includes.errors')
            @include('includes.success')
          </div>

          {{-- PASO 1 --}}
          <div class="section-head">
            <span class="badge-step" aria-hidden="true">1</span>
            <h2>Datos del titular</h2>
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <label for="ruex" class="form-label">RUEX</label>
              <input id="ruex" type="text" class="form-control form-control-xl" name="ruex" inputmode="numeric" readonly autocomplete="off" value="{{ $solicitud->filiacion }}" required>
            </div>

            <div class="col-md-6">
              <label for="email" class="form-label">Correo electrónico</label>
              <input id="email" type="email" class="form-control form-control-xl" name="email" autocomplete="email" readonly value="{{ $solicitud->email }}" required>
            </div>

            <div class="col-md-7">
              <label for="full_name" class="form-label">Nombre completo</label>
              <input id="full_name" type="text" class="form-control form-control-xl" name="full_name" autocomplete="name" readonly value="{{ $solicitud->nombre_completo }}" required>
            </div>

            <div class="col-md-2">
              <label for="amount" class="form-label">Monto</label>
              <input id="amount" type="text" class="form-control form-control-xl" name="amount" autocomplete="name" readonly value="100.00" required>
            </div>

            {{-- <div class="col-md-3">
              <label for="amount" class="form-label">Monto</label>
              <div class="input-group input-group-xl">
                <span class="input-group-text">$</span>
                <input id="amount" type="number" class="form-control" name="amount" step="0.01" min="0.01" inputmode="decimal" readonly value="100.00" required>
              </div>
              <div class="form-text">Mínimo 0.01</div>
            </div> --}}

            {{-- <div class="col-md-2">
              <label for="currency" class="form-label">Moneda</label>
              <select id="currency" class="form-select form-select-xl" name="currency" required>
                <option value="840" {{ old('currency','840')=='840'?'selected':'' }}>USD</option>
              </select>
            </div> --}}

            <div class="col-md-2">
              <label for="currency" class="form-label">Moneda</label>
              <input id="currency" type="text" class="form-control form-control-xl" name="currency" autocomplete="name" readonly value="USD" required>
            </div>

          </div>

          {{-- PASO 2 --}}
          <div class="section-head mt-4">
            <span class="badge-step" aria-hidden="true">2</span>
            <h2>Datos de la tarjeta</h2>
          </div>

          <div class="panel soft p-0 mb-2" aria-live="polite" aria-busy="true">
            <div id="creditcard-container">
              <div id="widget-loading" class="text-center py-5">
                <div class="spinner-border" role="status" aria-hidden="true"></div>
                <p class="mt-3 mb-0">Cargando pasarela de pago…</p>
              </div>
            </div>
          </div>

          <div id="widget-messages" class="alert alert-info d-none" role="alert"></div>

          {{-- PASO 3 --}}
          <div class="section-head mt-4">
            <span class="badge-step" aria-hidden="true">3</span>
            <h2>Confirmar y pagar</h2>
          </div>

          <div id="payment-form" class="mt-2">
            <form id="process-payment-form" novalidate>
              @csrf
              <input type="hidden" name="token" id="token-input">
              <input type="hidden" name="account_number" id="account-number-input">

              <div class="d-flex flex-wrap align-items-center gap-3">
                <button type="submit" class="btn btn-primary btn-xl fw-bold" id="btn-process" disabled>
                  <i class="bi bi-credit-card-2-front me-2" aria-hidden="true"></i>Procesar Pago
                </button>
                <div class="assurance"><i class="bi bi-shield-lock" aria-hidden="true"></i> Cifrado TLS · Cumplimiento PCI DSS</div>
              </div>
            </form>
            <input type="hidden" id="payment_token" name="payment_token">
          </div>

        </div>
      </div>

      {{-- DERECHA: RESUMEN --}}
      <aside class="col-12 col-lg-4 col-xl-4" aria-labelledby="summary-title">
        <div class="summary">
          <div class="s-head">
            <img src="{{ asset('images/LOGOconBorde200x229.png') }}" alt="" aria-hidden="true">
            <div>
              <h3 id="summary-title" class="h6 mb-0">Resumen</h3>
              <small class="text-muted">Revise antes de pagar</small>
            </div>
          </div>

          <div class="s-amount">
            <div>
              <div id="summary-amount" class="total">$0.00</div>
              <div id="summary-currency" class="currency">USD</div>
            </div>
            <div class="brands" aria-label="Tarjetas aceptadas">
              <img src="{{ asset('images/visa.svg') }}" alt="Visa" onerror="this.style.display='none'">
              <img src="{{ asset('images/mastercard.svg') }}" alt="Mastercard" onerror="this.style.display='none'">
            </div>
          </div>

          <ul class="s-list">
            <li><i class="bi bi-lock-fill" aria-hidden="true"></i> Pasarela certificada</li>
            <li><i class="bi bi-shield-check" aria-hidden="true"></i> Verificación antifraude</li>
            <li><i class="bi bi-clock-history" aria-hidden="true"></i> Operación en tiempo real</li>
          </ul>

          <hr>
          <small class="text-muted d-block">Si ocurre un error no se realiza el cargo. Recibirá confirmación por correo.</small>
        </div>
      </aside>
    </div>
  </div>

  @include('includes.confirmacionmodal')
  @include('includes.messagebasicmodal')
</div>
@endsection

@section('scripts')
<script>
  document.body.classList.add('payment','payment-page');

  // Config de pasarela (expuesta a payment.js)
  window.paymentConfig = {
    routes: {
      handleWidgetCallback: '{{ route("payment.handleWidgetCallback") }}',
      process:               '{{ route("payment.process") }}',
      success:               '{{ route("payment.success") }}',
      error:                 '{{ route("payment.error") }}'
    },
    csrfToken:  '{{ csrf_token() }}',
    apiKey:     '{{ config('payment.api_key') }}',
    testMode:   {{ config('payment.test_mode', true) ? 'true' : 'false' }},
    widgetUrls: {
      test: 'https://apicomponentv2-test.merchantprocess.net/UIComponent/CreditCard',
      prod: 'https://gateway.merchantprocess.net/securecomponent/v2/UIComponent/CreditCard'
    }
  };

  // Utilidad para que el widget avise el token de forma explícita
  // Llama window.paymentUI.setToken(token, accountNumber) desde payment.js o el callback del widget
  window.paymentUI = (function(){
    const $token = document.getElementById('token-input');
    const $acc   = document.getElementById('account-number-input');
    const $btn   = document.getElementById('btn-process');
    function syncButton(){ $btn.disabled = !($token.value && $token.value.length > 10); }
    return {
      setToken(token, accountNumber){
        if(token){ $token.value = token; }
        if(accountNumber){ $acc.value = accountNumber; }
        syncButton();
      },
      clear(){ $token.value=''; $acc.value=''; syncButton(); }
    };
  })();

  // Resumen dinámico (monto/moneda)
  (function(){
    const $amount  = document.getElementById('amount');
    const $currency= document.getElementById('currency');
    const $outAmt  = document.getElementById('summary-amount');
    const $outCur  = document.getElementById('summary-currency');
    const fmtUSD = v => (v ? Number(v).toLocaleString('en-US',{ style:'currency', currency:'USD' }) : '$0.00');

    function sync(){
      $outAmt.textContent = fmtUSD($amount.value);
      $outCur.textContent = ($currency.options[$currency.selectedIndex]?.text || 'USD').toUpperCase();
    }
    $amount?.addEventListener('input', sync);
    $currency?.addEventListener('change', sync);
    sync();
  })();
</script>

<script src="https://code.jquery.com/jquery-migrate-3.4.1.min.js"></script>
<script src="{{ asset('js/comun/confirmacionModal.js') }}"></script>
<script src="{{ asset('js/comun/messagebasicModal.js') }}"></script>
<script src="{{ asset('js/dist/payment/payment.js') }}"></script>
@endsection
