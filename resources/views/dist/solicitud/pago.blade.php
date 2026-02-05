@extends('layouts.admin')

@section('title', 'Confirmar Método de Pago')

@section('content')
<div class="col-lg-12">
    <div class="card border-0 shadow-lg mb-4">

        {{-- Header institucional --}}
        <div class="card-header text-center text-white py-4"
             style="background: linear-gradient(90deg, #003366, #0056b3);">
            <h3 class="fw-bold mb-1">
                Plataforma Oficial de Pagos
            </h3>
            <small>
                Servicio Nacional de Migración · Ministerio de Seguridad Pública · República de Panamá
            </small>
        </div>

        <div class="card-body p-5">

            {{-- Datos del solicitante --}}
            <div class="mb-5">
                <h5 class="fw-bold text-primary mb-4">
                    <i class="bi bi-person-vcard-fill me-2"></i>
                    Información del Solicitante
                </h5>

                <div class="row g-4">
                   

                    <div class="col-md-4">
                        <div class="border rounded-3 p-3 h-100 bg-light">
                            <small class="text-muted d-block">Nombre </small>
                            <div class="fs-5 fw-semibold">{{ $solicitud->primer_nombre }}</div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="border rounded-3 p-3 h-100 bg-light">
                            <small class="text-muted d-block">Apellido</small>
                            <div class="fs-5 fw-semibold">{{ $solicitud->primer_apellido }}</div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="border rounded-3 p-3 h-100 bg-light">
                            <small class="text-muted d-block">Pasaporte </small>
                            <div class="fs-5 fw-semibold">{{ $solicitud->pasaporte }}</div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="border rounded-3 p-3 h-100 bg-light">
                            <small class="text-muted d-block">RUEX</small>
                            <div class="fs-5 fw-semibold">{{ $solicitud->filiacion }}</div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="border rounded-3 p-3 h-100 bg-light">
                            <small class="text-muted d-block">Correo Electrónico</small>
                            <div class="fs-5 fw-semibold">{{ $solicitud->email }}</div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="border rounded-3 p-3 h-100 bg-light">
                            <small class="text-muted d-block">Teléfono</small>
                            <div class="fs-5 fw-semibold">{{ $solicitud->telefono }}</div>
                        </div>
                    </div>

                    
                </div>
            </div>

            {{-- Tarjeta del monto --}}
            <div class="mb-5">
                <div class="rounded-4 p-5 text-center text-white shadow"
                     style="background: linear-gradient(120deg, #198754, #20c997);">
                    <div class="mb-2 text-uppercase small">Monto a Pagar</div>
                    <div class="display-4 fw-bold">$100.00 USD</div>
                    <div class="small mt-2">
                        Pago correspondiente al trámite oficial de Cambio de Residencia
                    </div>
                </div>
            </div>

            {{-- Seguridad --}}
            <div class="alert border-0 shadow-sm d-flex align-items-center mb-5"
                 style="background-color: #e7f3ff;">
                <i class="bi bi-shield-lock-fill fs-3 me-3 text-primary"></i>
                <div>
                    Su pago será procesado mediante una pasarela certificada bajo estándares internacionales
                    de seguridad financiera (PCI Compliance).
                </div>
            </div>

            {{-- Botón de pago --}}
            <div class="text-center">
                <form method="POST" action="{{ route('payment.process') }}">
                    @csrf
                    <input type="hidden" name="solicitud_id" value="{{ $solicitud->id }}">
                    <input type="hidden" name="amount" value="100">
                    <input type="hidden" name="currency" value="USD">

                    <button class="btn btn-lg px-5 py-3 text-white fw-bold shadow"
                            style="background: linear-gradient(90deg, #003366, #0056b3); border-radius: 50px;">
                        <i class="bi bi-credit-card-2-front-fill me-2"></i>
                        Proceder al Pago Seguro
                    </button>
                </form>
            </div>

        </div>

        <div class="card-footer text-center small text-muted py-3">
            © {{ date('Y') }} Servicio Nacional de Migración · Plataforma Oficial de Pagos del Estado Panameño
        </div>

    </div>
</div>
@endsection
