{{-- resources/views/payment/error.blade.php --}}
@extends($layout ?? 'layouts.app')

@section('title', 'Pago Rechazado')

@section('styles')
<link href="{{ asset('css/payment/payment.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="payment-max">

  <div class="payment-hero" style="background-image: url('{{ asset('images/LOGOconBorde200x229.png') }}')">
    <div class="hero-content">
      <div class="lock-chip"><i class="fas fa-lock"></i> Pago Seguro</div>
      <h2 class="mt-3 mb-1">Transacción rechazada</h2>
      <p class="mb-0">No pudimos procesar tu pago. Revisa los detalles e intenta nuevamente.</p>
    </div>
  </div>

  @php
    // Datos que llegan por querystring
    $payload = [
      'message' => request('message'),
      'code'    => request('code'),
      'result'  => request('result'),
      'auth'    => request('auth'),
      'tid'     => request('tid'),
      'ref'     => request('ref'),
      'amount'  => request('amount'),
      'currency'=> request('currency'),
    ];
  @endphp

  <div class="row">
    <div class="col-xl-8 col-lg-7">
      <div class="payment-shell">

        <div class="text-center mb-3">
          <div class="text-danger mb-3" style="font-size:3.5rem">
            <i class="fas fa-times-circle"></i>
          </div>
          <h3 class="mb-1">No se pudo completar el pago</h3>
          <p class="text-muted mb-0">A continuación te mostramos el motivo reportado por el banco/gateway.</p>
        </div>

        <div class="border rounded p-3">
          <h6 class="text-muted mb-2">Detalle</h6>
          <p class="mb-2">{{ $payload['message'] ?? 'Transacción rechazada' }}</p>
          <ul class="small mb-0">
            @if(!empty($payload['code']))   <li><strong>Código:</strong> {{ $payload['code'] }}</li>@endif
            @if(!empty($payload['result'])) <li><strong>Resultado:</strong> {{ $payload['result'] }}</li>@endif
            @if(!empty($payload['auth']))   <li><strong>Autorización:</strong> {{ $payload['auth'] }}</li>@endif
            @if(!empty($payload['tid']))    <li><strong>Transacción:</strong> {{ $payload['tid'] }}</li>@endif
            @if(!empty($payload['ref']))    <li><strong>Referencia:</strong> {{ $payload['ref'] }}</li>@endif
            @if(!empty($payload['amount'])) <li><strong>Monto intentado:</strong>{{ number_format((float)($payload['amount'] ?? 0), 2) }}{{ ((string)($payload['currency'] ?? '') === '840') ? 'USD' : ($payload['currency'] ?? '') }}</li>@endif
          </ul>
        </div>

        <div class="d-flex flex-wrap gap-2 mt-4">
          <a href="{{ route('payment.tokenize') }}" class="btn btn-primary">
            <i class="fas fa-rotate-left me-2"></i> Intentar de nuevo
          </a>
          <a href="{{ url('/') }}" class="btn btn-outline-secondary">
            <i class="fas fa-home me-2"></i>Volver al inicio
          </a>
        </div>
      </div>
    </div>

    <div class="col-xl-4 col-lg-5">
      <div class="payment-summary">
        <h5 class="mb-2">Sugerencias</h5>
        <ul class="small ps-3 mb-0">
          <li>Verifica número, fecha de expiración y CVV.</li>
          <li>Consulta con tu banco si hay restricciones.</li>
          <li>Intenta con otra tarjeta.</li>
        </ul>
      </div>
    </div>
  </div>
</div>
@endsection
