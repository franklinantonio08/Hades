@extends('layouts.admin')
@section('title','Pago Completado')

@section('content')
<div class="col-lg-10 mx-auto">
  <div class="card border-0 shadow-lg">
    <div class="card-header text-white text-center py-4"
         style="background: linear-gradient(90deg,#198754,#20c997);">
      <h3 class="fw-bold mb-0">
        <i class="bi bi-check-circle-fill me-2"></i> Pago Completado
      </h3>
      <small>Servicio Nacional de Migración · República de Panamá</small>
    </div>

    <div class="card-body p-5">
      <div class="alert alert-success border-0 shadow-sm">
        Su pago fue procesado y confirmado exitosamente.
      </div>

      <div class="row g-3">
        <div class="col-md-6">
          <div class="border rounded-3 p-3 bg-light">
            <small class="text-muted d-block">Solicitante</small>
            <div class="fw-semibold fs-5">{{ $solicitud->nombre_completo }}</div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="border rounded-3 p-3 bg-light">
            <small class="text-muted d-block">RUEX</small>
            <div class="fw-semibold fs-5">{{ $solicitud->filiacion }}</div>
          </div>
        </div>

        @if(!empty($trx))
        <div class="col-md-6">
          <div class="border rounded-3 p-3 bg-light">
            <small class="text-muted d-block">Referencia</small>
            <div class="fw-semibold">{{ $trx->reference }}</div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="border rounded-3 p-3 bg-light">
            <small class="text-muted d-block">Autorización</small>
            <div class="fw-semibold">{{ $trx->authorization_number ?? '—' }}</div>
          </div>
        </div>
        @endif
      </div>

      <div class="text-center mt-5">
        <a href="/dist/solicitud" class="btn btn-lg text-white px-5 py-3 fw-bold shadow"
           style="background: linear-gradient(90deg,#003366,#0056b3); border-radius:50px;">
          <i class="bi bi-arrow-left-circle-fill me-2"></i>
          Volver a Mis Solicitudes
        </a>
      </div>
    </div>

    <div class="card-footer text-center small text-muted py-3">
      © {{ date('Y') }} Servicio Nacional de Migración
    </div>
  </div>
</div>
@endsection