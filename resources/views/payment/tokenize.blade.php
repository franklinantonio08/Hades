{{-- resources/views/payment/tokenize.blade.php --}}
@extends($layout ?? 'layouts.app')

@section('title', 'Método de Pago')

@section('styles')
<link href="{{ asset('css/payment/payment.css') }}" rel="stylesheet">

@endsection

@section('content')
<div class="payment-max">   {{-- ⬅️ nuevo wrapper ancho --}}
<div class="row justify-content-center">
  <div class="col-12">

    {{-- HERO con tu imagen --}}
    <div class="payment-hero" style="background-image: url('{{ asset('images/LOGOconBorde200x229.png') }}')">
      <div class="hero-content">
        <div class="lock-chip">
          <i class="fas fa-lock"></i> Pago Seguro
        </div>
        <h2 class="mt-3 mb-1">Método de Pago</h2>
        <p class="mb-0">Completa tus datos y procesa tu pago con total seguridad.</p>
      </div>
    </div>

    <div class="row g-4 align-items-start">
      {{-- Columna izquierda: formulario + widget --}}
      <div class="col-xl-9 col-lg-8">
        <div class="payment-shell">

          {{-- Paso 1: Datos del titular / compra --}}
          <div class="step-title">
            <div class="step-dot">1</div>
            <h4 class="m-0">Datos del titular</h4>
          </div>

          <div class="row g-4">
            <div class="col-md-6 mb-3">
              <label class="form-label">RUEX</label>
              <input type="text" class="form-control" name="ruex" autocomplete="off" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Correo Electrónico</label>
              <input type="email" class="form-control" name="email" autocomplete="email" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Nombre Completo</label>
              <input type="text" class="form-control" name="full_name" autocomplete="name" required>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Monto</label>
              <input type="number" class="form-control" name="amount" step="0.01" min="0.01" required>
            </div>
            <div class="col-md-3 mb-4">
              <label class="form-label">Moneda</label>
              <select class="form-control" name="currency" required>
                <option value="840">USD</option>
              </select>
            </div>
          </div>

          {{-- Paso 2: Tarjeta (widget) --}}
          <div class="step-title">
            <div class="step-dot">2</div>
            <h4 class="m-0">Datos de la tarjeta</h4>
          </div>

          <div class="card p-3 mb-3">
            <div id="creditcard-container">
              <div id="widget-loading" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Cargando pasarela de pago...</span>
                </div>
                <p class="mt-3">Cargando pasarela de pago...</p>
              </div>
            </div>
          </div>

          <div id="widget-messages" class="alert alert-info mt-3" style="display:none;"></div>

          {{-- Paso 3: Confirmar y pagar --}}
          <div class="step-title">
            <div class="step-dot">3</div>
            <h4 class="m-0">Confirmar y pagar</h4>
          </div>

          <div id="payment-form" class="mt-3">
            <form id="process-payment-form">
              @csrf
              <input type="hidden" name="token" id="token-input">
              <input type="hidden" name="account_number" id="account-number-input">

              <div class="text-center">
                <button type="submit" class="btn btn-primary btn-lg fw-bold" id="btn-process">
                  <i class="fas fa-credit-card me-2"></i>Procesar Pago
                </button>
              </div>
            </form>
            <input type="hidden" id="payment_token" name="payment_token">
          </div>
        </div>
      </div>

      {{-- Columna derecha: resumen y señales de confianza --}}
      <div class="col-xl-3 col-lg-4"> 
        <div class="payment-summary">
          <h5 class="mb-2">Resumen</h5>
          <div class="summary-row mb-2">
            <span class="summary-amount" id="summary-amount">$0.00</span>
            <span class="text-muted" id="summary-currency">USD</span>
          </div>
          <small class="text-muted d-block mb-3">El total se actualizará según el monto ingresado.</small>

          <hr>

          <h6 class="mb-2">Tarjetas aceptadas</h6>
          <div class="badges">
            <span class="badge-soft">VISA</span>
            <span class="badge-soft">MASTERCARD</span>
          </div>

          
        </div>
      </div>
    </div>

  </div>
</div>
</div>
@include('includes.errordepagoModal')

@endsection

@section('scripts')
<script>
    // Configuración global que usará payment.js
    window.paymentConfig = {
        routes: {
            handleWidgetCallback: '{{ route("payment.handleWidgetCallback") }}',
            process: '{{ route("payment.process") }}',
            success: '{{ route("payment.success") }}',
            error:   '{{ route("payment.error") }}' 
        },
        csrfToken: '{{ csrf_token() }}',
        apiKey: '{{ config('payment.api_key') }}',
        testMode: {{ config('payment.test_mode', true) ? 'true' : 'false' }},
        widgetUrls: {
            test: 'https://apicomponentv2-test.merchantprocess.net/UIComponent/CreditCard',
            prod: 'https://gateway.merchantprocess.net/securecomponent/v2/UIComponent/CreditCard'
        }
    };
</script>
<script src="https://code.jquery.com/jquery-migrate-3.4.1.min.js"></script>
<script src="{{ asset('js/dist/payment/payment.js') }}"></script>
@endsection
