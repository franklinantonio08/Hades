@extends('layouts.admin')
@section('title','Confirmando Pago')

@section('content')
<div class="col-lg-8 mx-auto">
  <div class="card border-0 shadow-lg">
    <div class="card-header text-center text-white py-4"
         style="background: linear-gradient(90deg, #003366, #0056b3);">
      <h3 class="fw-bold mb-1">
        <i class="bi bi-hourglass-split me-2"></i>
        Confirmando Pago
      </h3>
      <small>Servicio Nacional de Migración · República de Panamá</small>
    </div>

    <div class="card-body p-5 text-center">
      <div class="mb-3">
        <div class="spinner-border" role="status"></div>
      </div>
      <div class="fw-semibold fs-5">Estamos confirmando tu transacción…</div>
      <div class="text-muted mt-2">
        Esto puede tardar unos segundos. No cierres esta ventana.
      </div>

      <div class="mt-4 text-muted small">
        Solicitud #{{ $solicitud->id ?? '—' }} · RUEX {{ $solicitud->filiacion ?? '—' }}
      </div>
    </div>

    <div class="card-footer text-center small text-muted py-3">
      Esta pantalla se actualizará automáticamente.
    </div>
  </div>
</div>

<script>
  // refresca cada 3 segundos (ajusta si quieres)
  setTimeout(() => window.location.reload(), 3000);
</script>
@endsection