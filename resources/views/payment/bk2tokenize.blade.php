{{-- resources/views/payment/tokenize.blade.php --}}
@extends($layout ?? 'layouts.app')

@section('title', 'Agregar Método de Pago')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-12">
        <!-- Contenedor del Widget -->
        <div id="creditcard-container">
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando widget...</span>
                </div>
                <p class="mt-3 text-muted">Cargando formulario seguro de pago</p>
            </div>
        </div>
        
        <!-- Mensajes de estado -->
        <div id="widget-messages" class="alert alert-info" style="display: none;"></div>
        
        <!-- Formulario para procesar pagos (se mostrará después de tokenizar) -->
        <div id="payment-form" class="mt-4 p-4 border rounded" style="display: none;">
            <h5 class="mb-4">Tarjeta Tokenizada</h5>
            <div class="card mb-4">
                <div class="card-body">
                    <p><strong>Tarjeta:</strong> <span id="card-brand"></span> terminada en <span id="card-last-four"></span></p>
                    <p><strong>Titular:</strong> <span id="cardholder-name"></span></p>
                    <p><strong>Expira:</strong> <span id="card-expiry"></span></p>
                </div>
            </div>
            
            <form id="process-payment-form">
                @csrf
                <input type="hidden" name="token" id="token-input">
                <input type="hidden" name="account_number" id="account-number-input">
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Monto</label>
                        <input type="number" class="form-control" name="amount" 
                               step="0.01" min="0.01" required>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <label class="form-label">Moneda</label>
                        <select class="form-control" name="currency" required>
                            <option value="USD">USD - Dólar Americano</option>
                            <option value="GTQ">GTQ - Quetzal Guatemalteco</option>
                        </select>
                    </div>
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-payment">
                        <i class="fas fa-credit-card me-2"></i>Procesar Pago
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
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
        ? 'https://apicomponentv2-test.merchantprocess.net/UIComponent/CreditCard'
        : 'https://gateway.merchantprocess.net/securecomponent/v2/UIComponent/CreditCard';

    console.log('Cargando widget desde:', widgetUrl);

    // Cargar el widget
    $.ajax({
        type: "GET",
        url: widgetUrl,
        data: {
            APIKey: "{{ config('payment.api_key') }}",
            Culture: "es"
        },
        dataType: "html", // 👈 forzamos HTML
        success: function(response) {
            console.log('Widget cargado exitosamente');

            // 🔹 Eliminar el script de jQuery 1.8.3 que rompe tu jQuery 3.6
            var sanitized = response.replace(
                /<script[^>]*src=["']https:\/\/ajax\.googleapis\.com\/ajax\/libs\/jquery\/1\.8\.3\/jquery\.min\.js["'][^>]*><\/script>/i,
                ''
            );

            // 🔹 Parsear el HTML para que los <script> se ejecuten
            var parsed = $.parseHTML(sanitized, document, true);

            // 🔹 Insertar el contenido en tu contenedor
            $("#creditcard-container").empty().append(parsed);

            showMessage('Widget cargado correctamente. Ingrese los datos de su tarjeta.', 'info');
        },
        error: function(xhr, status, error) {
            console.error('Error cargando widget:', error);
            console.log('Status:', status);
            console.log('Response:', xhr.responseText.substring(0, 500)); 

            $("#creditcard-container").html('<div class="alert alert-danger">Error al cargar el widget de pago</div>');
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
            // Redirigir a página de éxito
            setTimeout(() => {
                window.location.href = '{{ route("payment.success") }}?transaction=' + (result.transaction?.id || '');
            }, 2000);
        } else {
            showMessage('Error en el pago: ' + result.message, 'danger');
        }
    } catch (error) {
        showMessage('Error de conexión: ' + error.message, 'danger');
    }
});
</script>
@endsection