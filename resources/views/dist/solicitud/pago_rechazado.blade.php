@extends('layouts.admin')
@section('title','Pago Rechazado')

@section('content')
<div class="col-lg-10 mx-auto">
    <div class="card border-0 shadow-lg mb-4">

        {{-- Header institucional --}}
        <div class="card-header text-center text-white py-4"
             style="background: linear-gradient(90deg, #8b0000, #dc3545);">
            <h3 class="fw-bold mb-1">
                <i class="bi bi-x-circle-fill me-2"></i>
                Pago Rechazado
            </h3>
            <small>
                Servicio Nacional de Migración · Ministerio de Seguridad Pública · República de Panamá
            </small>
        </div>

        <div class="card-body p-5">

            <div class="alert alert-danger border-0 shadow-sm d-flex align-items-start">
                <i class="bi bi-exclamation-triangle-fill fs-3 me-3"></i>
                <div>
                    <div class="fw-bold">No fue posible completar la transacción.</div>
                    <div class="small mt-1">
                        El pago fue rechazado o no pudo ser autorizado. Puede intentar nuevamente o validar con su entidad financiera.
                    </div>
                </div>
            </div>

            {{-- Datos del solicitante --}}
            <div class="mt-4">
                <h5 class="fw-bold text-danger mb-3">
                    <i class="bi bi-person-vcard-fill me-2"></i>
                    Información de la Solicitud
                </h5>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 bg-light h-100">
                            <small class="text-muted d-block">Solicitante</small>
                            <div class="fw-semibold fs-5">{{ $solicitud->nombre_completo ?? '—' }}</div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="border rounded-3 p-3 bg-light h-100">
                            <small class="text-muted d-block">RUEX</small>
                            <div class="fw-semibold fs-5">{{ $solicitud->filiacion ?? '—' }}</div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="border rounded-3 p-3 bg-light h-100">
                            <small class="text-muted d-block">Correo</small>
                            <div class="fw-semibold fs-6">{{ $solicitud->email ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Datos de transacción (si existe) --}}
            <div class="mt-4">
                <h6 class="fw-bold text-danger mb-3">
                    <i class="bi bi-receipt-cutoff me-2"></i>
                    Detalles de la Transacción
                </h6>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 bg-light h-100">
                            <small class="text-muted d-block">Referencia</small>
                            <div class="fw-semibold">{{ $trx->reference ?? '—' }}</div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="border rounded-3 p-3 bg-light h-100">
                            <small class="text-muted d-block">Estado</small>
                            <div class="fw-semibold text-danger">{{ strtoupper($trx->status ?? 'rechazado') }}</div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="border rounded-3 p-3 bg-light h-100">
                            <small class="text-muted d-block">Código respuesta</small>
                            <div class="fw-semibold">{{ $trx->response_code ?? '—' }}</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 bg-light h-100">
                            <small class="text-muted d-block">Fecha/Hora</small>
                            <div class="fw-semibold">
                                {{ !empty($trx->response_date) ? \Illuminate\Support\Carbon::parse($trx->response_date)->format('Y-m-d H:i') : '—' }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="border rounded-3 p-3 bg-light h-100">
                            <small class="text-muted d-block">Monto</small>
                            <div class="fw-semibold">
                                @if(!empty($trx))
                                    ${{ number_format((float)$trx->amount, 2) }} {{ $trx->currency ?? 'USD' }}
                                @else
                                    —
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-muted small mt-3">
                    Si el problema persiste, intente con otra tarjeta o contacte a su banco. También puede comunicarse con soporte institucional.
                </div>
            </div>

            {{-- Acciones --}}
            <div class="d-flex flex-column flex-md-row gap-3 justify-content-center mt-5">
                <a href="{{ url('/dist/solicitud/pago/'.$solicitud->id) }}"
                   class="btn btn-lg px-5 py-3 fw-bold text-white shadow"
                   style="background: linear-gradient(90deg, #003366, #0056b3); border-radius:50px;">
                    <i class="bi bi-arrow-repeat me-2"></i>
                    Reintentar Pago
                </a>

                <a href="{{ url('/dist/solicitud') }}"
                   class="btn btn-lg px-5 py-3 fw-bold shadow"
                   style="border-radius:50px;">
                    <i class="bi bi-list-check me-2"></i>
                    Volver a Mis Solicitudes
                </a>
            </div>

        </div>

        <div class="card-footer text-center small text-muted py-3">
            © {{ date('Y') }} Servicio Nacional de Migración · Plataforma Oficial de Pagos
        </div>

    </div>
</div>
@endsection