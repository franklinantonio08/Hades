{{-- resources/views/payment/tokenize.blade.php --}}
@extends($layout ?? 'layouts.app')

@section('title', 'Método de Pago')

@section('styles')
<style>
/* Evitar que el usuario interactúe directamente con el iframe */
iframe#creditcard-container .save-button,
iframe#creditcard-container .close-button {
    display: none !important;
}

iframe#creditcard-container {

    width: 100%;
    min-height: 650px;
    border: none;
    overflow: hidden;
}
</style>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-12">
        
        <div class="col-md-12">
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
        </div>
        <div class="col-md-12">
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
        </div>

        <h4>Datos del Tarjetahabiente</h4>
        <div class="card p-3 mb-3">
            <iframe id="creditcard-container"
                    src="{{ config('payment.test_mode', true) 
                            ? 'https://apicomponentv2-test.merchantprocess.net/UIComponent/CreditCard?APIKey=' . config('payment.api_key') . '&Culture=es&showSaveButton=false&showCloseButton=false'
                            : 'https://gateway.merchantprocess.net/securecomponent/v2/UIComponent/CreditCard?APIKey=' . config('payment.api_key') . '&Culture=es' }}">
            </iframe>
        </div>

        <!-- Mensajes de estado -->
        <div id="widget-messages" class="alert alert-info mt-3" style="display: none;"></div>

        <!-- Formulario para procesar pagos -->
        <div id="payment-form" class="mt-4 p-4 border rounded">
            <form id="process-payment-form">
                @csrf
                <input type="hidden" name="token" id="token-input">
                <input type="hidden" name="account_number" id="account-number-input">


                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold" id="btn-process">
                        <i class="fas fa-credit-card me-2"></i>Continuar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Variables globales
var cardToken, accountNumber, cardholderName, associatedCard, cardExpiry;

// Capturar mensajes del iframe (tokenización)
window.addEventListener("message", function(event) {
    if (event.data?.TokenDetails) {
        cardToken = event.data.TokenDetails.AccountToken;
        accountNumber = event.data.TokenDetails.AccountNumber;
        cardholderName = event.data.TokenDetails.CardHolderName;
        associatedCard = event.data.TokenDetails.CardNumber;
        cardExpiry = event.data.TokenDetails.ExpirationDate || null;

        // Llenar campos ocultos
        document.getElementById('token-input').value = cardToken;
        document.getElementById('account-number-input').value = accountNumber;

        showMessage('¡Tarjeta tokenizada exitosamente!', 'success');
    }
});

// Función para mostrar mensajes
function showMessage(message, type) {
    const msgDiv = document.getElementById('widget-messages');
    msgDiv.textContent = message;
    msgDiv.className = `alert alert-${type}`;
    msgDiv.style.display = 'block';
    setTimeout(() => { msgDiv.style.display = 'none'; }, 5000);
}

// Detectar marca de tarjeta
function detectCardBrand(number) {
    if (!number) return 'Tarjeta';
    number = number.replace(/\s+/g, '');
    if (/^4/.test(number)) return 'Visa';
    if (/^5[1-5]/.test(number)) return 'Mastercard';
    if (/^3[47]/.test(number)) return 'American Express';
    if (/^6(?:011|5)/.test(number)) return 'Discover';
    if (/^3(?:0[0-5]|[68])/.test(number)) return 'Diners Club';
    return 'Tarjeta';
}

// Guardar token en el servidor
async function saveTokenToServer() {
    try {
        const response = await fetch('{{ route("payment.handleWidgetCallback") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                token: cardToken,
                account_number: accountNumber,
                cardholder_name: cardholderName,
                last_four: associatedCard ? associatedCard.slice(-4) : '',
                brand: detectCardBrand(associatedCard),
                expiry_date: cardExpiry
            })
        });
        return await response.json();
    } catch (e) {
        showMessage('Error de conexión al guardar la tarjeta', 'danger');
        return { success: false, message: e.message };
    }
}

// Enviar todo al procesar pago
document.getElementById('process-payment-form').addEventListener('submit', async function(e) {
    e.preventDefault();

    // Validar token
    if (!cardToken) {
        showMessage('Debe ingresar y validar la tarjeta primero.', 'warning');
        return;
    }

    // Guardar token
    const tokenResult = await saveTokenToServer();
    if (!tokenResult.success) return;

    // Procesar pago
    const formData = new FormData(this);
    formData.append('token', cardToken);
    formData.append('account_number', accountNumber);

    try {
        const response = await fetch('{{ route("payment.process") }}', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const result = await response.json();
        if (result.success) {
            showMessage('¡Pago procesado exitosamente!', 'success');
            setTimeout(() => { window.location.href = '{{ route("payment.success") }}?transaction=' + (result.transaction?.id || ''); }, 2000);
        } else {
            showMessage('Error en el pago: ' + result.message, 'danger');
        }
    } catch (error) {
        showMessage('Error de conexión: ' + error.message, 'danger');
    }
});
</script>
@endsection
