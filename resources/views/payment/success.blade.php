{{-- resources/views/payment/success.blade.php --}}
@extends($layout ?? 'layouts.app')

@section('title', 'Pago Exitoso')

@section('styles')
<link href="{{ asset('css/payment/payment.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="payment-max">

  {{-- HERO seguro --}}
  <div class="payment-hero" style="background-image: url('{{ asset('images/LOGOconBorde200x229.png') }}')">
    <div class="hero-content">
      <div class="lock-chip"><i class="fas fa-lock"></i> Pago Seguro</div>
      <h2 class="mt-3 mb-1">¡Pago exitoso!</h2>
      <p class="mb-0">Tu transacción se procesó correctamente.</p>
    </div>
  </div>

  <div class="row">
    <div class="col-xl-8 col-lg-7">
      <div class="payment-shell">

        <div class="text-center mb-3">
          <div class="text-success mb-3" style="font-size:3.5rem">
            <i class="fas fa-check-circle"></i>
          </div>
          <h3 class="mb-1">Gracias por tu pago</h3>
          <p class="text-muted mb-0">Te mostramos el resumen de la operación.</p>
        </div>

        @php
          // $transaction lo entrega el controlador; puede venir null si no encuentra id
          $t = $transaction ?? null;
        @endphp

        <div class="row g-3 mt-2">
          <div class="col-md-6">
            <div class="border rounded p-3 h-100">
              <h6 class="text-muted mb-2">Monto</h6>
              <div class="d-flex align-items-baseline gap-2">
                <span class="summary-amount">
                  {{ $t ? number_format($t->amount, 2) : '0.00' }}
                </span>
                <span class="text-muted">{{'USD' }}</span>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="border rounded p-3 h-100">
              <h6 class="text-muted mb-2">Fecha</h6>
              <div>{{ $t ? $t->created_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}</div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="border rounded p-3 h-100">
              <h6 class="text-muted mb-2">Código de Autorización</h6>
              <div class="fw-semibold">{{ $t->authorization_code ?? '—' }}</div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="border rounded p-3 h-100">
              <h6 class="text-muted mb-2">ID Transacción (Gateway)</h6>
              <div class="fw-semibold">{{ $t->gateway_transaction_id ?? '—' }}</div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="border rounded p-3 h-100">
              <h6 class="text-muted mb-2">Referencia</h6>
              <div class="fw-semibold">{{ $t->reference ?? '—' }}</div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="border rounded p-3 h-100">
              <h6 class="text-muted mb-2">Terminación de tarjeta</h6>
              <div class="fw-semibold">
                @if(!empty($t?->token?->last_four_digits))
                  •••• {{ $t->token->last_four_digits }}
                @else
                  — 
                @endif
              </div>
            </div>
          </div>
        </div>

        <div class="d-flex flex-wrap gap-2 mt-4">
          <a href="{{ url('/') }}" class="btn btn-primary">
            <i class="fas fa-home me-2"></i>Volver al inicio
          </a>
          <a href="{{ route('payment.tokenize') }}" class="btn btn-outline-secondary">
            <i class="fas fa-credit-card me-2"></i>Nuevo pago
          </a>
        </div>
      </div>
    </div>

    {{-- caja lateral de “Estado”/confianza --}}
    <div class="col-xl-4 col-lg-5">
      <div class="payment-summary">
        <h5 class="mb-2">Estado de la operación</h5>
        <div class="d-flex align-items-center gap-2 text-success fw-semibold">
          <i class="fas fa-shield-check"></i> Aprobada
        </div>
        <hr>
        <div class="badges">
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
