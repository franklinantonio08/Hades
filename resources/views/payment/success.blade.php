{{-- resources/views/payment/success.blade.php --}}
@extends($layout ?? 'layouts.app')

@section('title', 'Pago Exitoso')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-lg">
            <div class="card-body text-center py-5">
                <div class="text-success mb-4" style="font-size: 4rem;">
                    <i class="fas fa-check-circle"></i>
                </div>
                
                <h2 class="mb-3">¡Pago Exitoso!</h2>
                <p class="lead text-muted mb-4">Su transacción ha sido procesada correctamente.</p>
                
                @if(request()->has('transaction'))
                <div class="alert alert-info">
                    <h6>📋 Detalles de la Transacción</h6>
                    <p class="mb-1"><strong>Número de transacción:</strong> {{ request()->query('transaction') }}</p>
                    <p class="mb-0"><strong>Fecha:</strong> {{ now()->format('d/m/Y H:i') }}</p>
                </div>
                @endif
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                    <a href="{{ url('/') }}" class="btn btn-primary">
                        <i class="fas fa-home me-2"></i>Volver al Inicio
                    </a>
                    <a href="{{ route('payment.tokenize') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-credit-card me-2"></i>Nuevo Pago
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection