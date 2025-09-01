{{-- resources/views/payment/tokenize.blade.php --}}
@extends($layout ?? 'layouts.app')

@section('title', 'Método de Pago')

@section('styles')
<link href="{{ asset('css/payment/payment.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-12">

        <!-- Datos generales -->
        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label">RUEX</label>
                <input type="text" class="form-control" name="ruex" required>
            </div>
            <div class="col-md-12 mb-3">
                <label class="form-label">Nombre Completo</label>
                <input type="text" class="form-control" name="full_name" required>
            </div>
            <div class="col-md-12 mb-3">
                <label class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" name="email" required>
            </div>
        </div>

        <!-- Monto y moneda -->
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Monto</label>
                <input type="number" class="form-control" name="amount" step="0.01" min="0.01" required>
            </div>
            <div class="col-md-6 mb-4">
                <label class="form-label">Moneda</label>
                <select class="form-control" name="currency" required>
                    <option value="USD">USD - Dólar Americano</option>
                </select>
            </div>
        </div>

        <h4>Datos del Tarjetahabiente</h4>
        <div class="card p-3 mb-3">
            <div id="creditcard-container">
                {{-- ❌ REMUEVE el iframe estático --}}
                {{-- ✅ Se cargará DINÁMICAMENTE después de que JS esté listo --}}
                <div id="widget-loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando pasarela de pago...</span>
                    </div>
                    <p class="mt-3">Cargando pasarela de pago...</p>
                </div>
            </div>
        </div>

        <div id="widget-messages" class="alert alert-info mt-3" style="display: none;"></div>

        <!-- Formulario para procesar pagos -->
        <div id="payment-form" class="mt-4 p-4 border rounded">
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
        </div>

        <div class="mt-3">
            <button onclick="testRealToken()" class="btn btn-success btn-sm">
                🧪 Probar con Token Real
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    window.paymentConfig = {
        routes: {
            handleWidgetCallback: '{{ route("payment.handleWidgetCallback") }}',
            process: '{{ route("payment.process") }}',
            success: '{{ route("payment.success") }}'
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
<script src="{{ asset('js/dist/payment/payment.js') }}"></script>
<script>
    function testRealToken() {
        // Simular la respuesta REAL del widget
        const realResponse = {
            TokenDetails: {
                AccountToken: 'd64d152d-ceef-4c70-8bf1-f673229ee63f', // Token REAL
                AccountNumber: '123456789',
                CardHolderName: 'Jonathan De Bello',
                CardNumber: '5240013861347542',
                ExpirationDate: '12/2025'
            }
        };
        
        console.log('🧪 Probando con token REAL:', realResponse.TokenDetails.AccountToken);
        
        // Llamar al callback manualmente
        if (window.SaveCreditCard_SuccessCallback) {
            window.SaveCreditCard_SuccessCallback(realResponse);
        } else {
            alert('❌ Callback no disponible. Recarga la página.');
        }
    }
    </script>
@endsection