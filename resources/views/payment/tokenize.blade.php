@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Agregar Método de Pago</div>

                <div class="card-body">
                    <!-- Contenedor del Widget -->
                    <div id="creditcard-container"><!-- AQUÍ SE DESPLEGARÁ EL FORMULARIO --></div>
                    
                    <!-- Mensajes de estado -->
                    <div id="widget-messages" class="alert alert-info mt-3" style="display: none;"></div>
                    
                    <!-- Formulario para procesar pagos (se mostrará después de tokenizar) -->
                    <div id="payment-form" class="mt-3" style="display: none;">
                        <h5>Tarjeta Tokenizada</h5>
                        <div class="card">
                            <div class="card-body">
                                <p><strong>Tarjeta:</strong> <span id="card-brand"></span> terminada en <span id="card-last-four"></span></p>
                                <p><strong>Titular:</strong> <span id="cardholder-name"></span></p>
                                <p><strong>Expira:</strong> <span id="card-expiry"></span></p>
                            </div>
                        </div>
                        
                        <form id="process-payment-form" class="mt-3">
                            @csrf
                            <input type="hidden" name="token" id="token-input">
                            <input type="hidden" name="account_number" id="account-number-input">
                            
                            <div class="form-group mb-3">
                                <label>Monto</label>
                                <input type="number" class="form-control" name="amount" 
                                       step="0.01" min="0.01" required>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label>Moneda</label>
                                <select class="form-control" name="currency" required>
                                    <option value="USD">USD</option>
                                    <option value="GTQ">GTQ</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-success">Procesar Pago</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Variables globales para los callbacks
var cardToken;
var accountNumber;
var cardholderName;
var associatedCard;
var cardExpiry;

// Función de éxito - cuando se tokeniza exitosamente
function SaveCreditCard_SuccessCallback(response) {
    console.log('Tokenización exitosa:', response);
    
    // Capturar los datos importantes
    cardToken = response.TokenDetails.AccountToken;
    accountNumber = response.TokenDetails.AccountNumber;
    cardholderName = response.TokenDetails.CardHolderName;
    associatedCard = response.TokenDetails.CardNumber;
    
    // Extraer fecha de expiración si está disponible
    if (response.TokenDetails.ExpirationDate) {
        cardExpiry = response.TokenDetails.ExpirationDate;
    }
    
    // Mostrar información al usuario
    showMessage('¡Tarjeta tokenizada exitosamente!', 'success');
    
    // Llenar los campos ocultos
    document.getElementById('token-input').value = cardToken;
    document.getElementById('account-number-input').value = accountNumber;
    
    // Mostrar información de la tarjeta
    document.getElementById('card-brand').textContent = detectCardBrand(associatedCard);
    document.getElementById('card-last-four').textContent = associatedCard.slice(-4);
    document.getElementById('cardholder-name').textContent = cardholderName;
    
    if (cardExpiry) {
        document.getElementById('card-expiry').textContent = cardExpiry;
    }
    
    // Mostrar formulario de pago
    document.getElementById('payment-form').style.display = 'block';
    
    // Enviar datos al servidor
    saveTokenToServer();
}

// Función de fallo - cuando hay error en tokenización
function SaveCreditCard_FailureCallback(response) {
    console.error('Error en tokenización:', response);
    showMessage('Error: ' + response.ResponseDescription, 'danger');
}

// Función de cancelación - cuando el usuario cancela
function SaveCreditCard_CancelCallback() {
    console.log("Usuario canceló la operación");
    showMessage('Operación cancelada por el usuario', 'warning');
}

// Función para detectar la marca de la tarjeta
function detectCardBrand(cardNumber) {
    if (!cardNumber) return 'Tarjeta';
    
    cardNumber = cardNumber.replace(/\s+/g, '');
    
    if (/^4/.test(cardNumber)) return 'Visa';
    if (/^5[1-5]/.test(cardNumber)) return 'Mastercard';
    if (/^3[47]/.test(cardNumber)) return 'American Express';
    if (/^6(?:011|5)/.test(cardNumber)) return 'Discover';
    if (/^3(?:0[0-5]|[68])/.test(cardNumber)) return 'Diners Club';
    
    return 'Tarjeta';
}

// Función para mostrar mensajes
function showMessage(message, type) {
    const messageDiv = document.getElementById('widget-messages');
    messageDiv.textContent = message;
    messageDiv.className = `alert alert-${type}`;
    messageDiv.style.display = 'block';
    
    // Ocultar mensaje después de 5 segundos
    setTimeout(() => {
        messageDiv.style.display = 'none';
    }, 5000);
}

// Función para guardar el token en el servidor
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
                expiry_date: cardExpiry || null
            })
        });

        const result = await response.json();
        
        if (!result.success) {
            console.error('Error guardando token:', result.message);
            showMessage('Error al guardar la tarjeta: ' + result.message, 'danger');
        }
    } catch (error) {
        console.error('Error de conexión:', error);
        showMessage('Error de conexión al guardar la tarjeta', 'danger');
    }
}

// Cargar el widget cuando el documento esté listo
$(document).ready(function() {
    // Determinar la URL del widget según el ambiente
    const isTestMode = {{ config('payment.test_mode', true) ? 'true' : 'false' }};
    const widgetUrl = isTestMode 
        ? '{{ config("payment.widget_url_test") }}'
        : '{{ config("payment.widget_url_prod") }}';

    console.log('Cargando widget desde:', widgetUrl);
    console.log('API Key:', '{{ config("payment.api_key") }}');

    // Cargar el widget
    $.ajax({
        type: "GET",
        url: widgetUrl,
        data: {
            APIKey: "{{ config('payment.api_key') }}",
            Culture: "es"
        },
        success: function(jsonResponse) {
            console.log('Widget cargado exitosamente');
            $("#creditcard-container").html(jsonResponse);
            showMessage('Widget cargado correctamente. Ingrese los datos de su tarjeta.', 'info');
        },
        error: function(xhr, status, error) {
            console.error('Error cargando widget:', error, xhr);
            showMessage('Error al cargar el formulario de pago. Consulte la consola para más detalles.', 'danger');
        }
    });
});

// Procesar pago
document.getElementById('process-payment-form')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch('{{ route("payment.process") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const result = await response.json();
        
        if (result.success) {
            showMessage('¡Pago procesado exitosamente!', 'success');
            // Redirigir a página de éxito si existe la ruta
            @if (Route::has('payment.success'))
                setTimeout(() => {
                    window.location.href = '{{ route("payment.success") }}?transaction=' + (result.transaction?.id || '');
                }, 2000);
            @endif
        } else {
            showMessage('Error en el pago: ' + result.message, 'danger');
        }
    } catch (error) {
        showMessage('Error de conexión: ' + error.message, 'danger');
    }
});
</script>
@endsection

@section('styles')
<style>
    /* Estilos mínimos para probar */
    body { font-family: Arial, sans-serif; padding: 20px; background: #f8f9fa; }
    .card { background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .card-header { background: #007bff; color: white; padding: 15px; border-radius: 8px 8px 0 0; }
    .card-body { padding: 20px; }
    .form-group { margin-bottom: 15px; }
    .form-control { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
    .btn { padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
    .btn-primary { background: #007bff; color: white; }
    .btn-success { background: #28a745; color: white; }
    .alert { padding: 10px; border-radius: 4px; margin-bottom: 15px; }
    .alert-info { background: #d1ecf1; color: #0c5460; }
    .alert-success { background: #d4edda; color: #155724; }
    .alert-danger { background: #f8d7da; color: #721c24; }
</style>
@endsection