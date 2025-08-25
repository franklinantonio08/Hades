<?php
// app/Http/Controllers/Dist/PaymentController.php
namespace App\Http\Controllers\Dist;

use App\Http\Controllers\Controller;
use App\Services\PaymentGatewayService;
use App\Models\PaymentToken;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentGatewayService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    // Mostrar formulario con Widget
    public function showTokenizationForm()
    {
        //Depuración temporal
        \Log::info('Accediendo a showTokenizationForm');
        \Log::info('Usuario autenticado: ' . (auth()->check() ? 'Sí' : 'No'));
        \Log::info('User ID: ' . (auth()->check() ? auth()->id() : 'N/A'));
        
        if (!auth()->check()) {
            \Log::warning('Usuario no autenticado, redirigiendo a login');
        }
        
        return view('payment.tokenize', [
            'api_key' => config('payment.api_key'),
            'layout' => 'layouts.payment' // Usar layout de pagos
        ]);
       // Vista mínima sin layout
    //    return response()->view('payment.minimal-tokenize', [
    //     'api_key' => config('payment.api_key')
    // ]);
    
    }

    // Procesar callback del Widget
    public function handleWidgetCallback(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'account_number' => 'required|string',
            'cardholder_name' => 'required|string',
            'last_four' => 'required|digits:4',
            'brand' => 'required|string',
            'expiry_date' => 'nullable|date_format:Y-m'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Verificar si el token ya existe
            $existingToken = PaymentToken::where('token', $request->token)
                                        ->where('user_id', auth()->id())
                                        ->first();

            if ($existingToken) {
                return response()->json([
                    'success' => true,
                    'message' => 'Token ya existente',
                    'token' => $existingToken
                ]);
            }

            // Guardar token en base de datos
            $token = PaymentToken::create([
                'user_id' => auth()->id(),
                'token' => $request->token,
                'account_number' => $request->account_number,
                'last_four_digits' => $request->last_four,
                'brand' => $request->brand,
                'cardholder_name' => $request->cardholder_name,
                'expiry_date' => $request->expiry_date ? $request->expiry_date . '-01' : null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tarjeta guardada exitosamente',
                'token' => $token
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en widget callback: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la tarjeta: ' . $e->getMessage()
            ], 500);
        }
    }

    // Procesar pago con token
    public function processPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'account_number' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|size:3'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Verificar que el token pertenece al usuario
            $token = PaymentToken::where('token', $request->token)
                                ->where('user_id', auth()->id())
                                ->firstOrFail();

            // Procesar la transacción
            $response = $this->paymentService->sale(
                $request->token,
                $request->amount,
                $request->currency,
                uniqid('txn_')
            );

            // Guardar transacción
            $transaction = PaymentTransaction::create([
                'user_id' => auth()->id(),
                'token_id' => $token->id,
                'amount' => $request->amount,
                'currency' => $request->currency,
                'reference' => $response['reference'] ?? uniqid('txn_'),
                'status' => $response['status'] ?? 'completed',
                'request_data' => $request->all(),
                'response_data' => $response,
                'gateway_transaction_id' => $response['transactionId'] ?? null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pago procesado exitosamente',
                'transaction' => $transaction
            ]);

        } catch (\Exception $e) {
            \Log::error('Error procesando pago: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el pago: ' . $e->getMessage()
            ], 500);
        }
    }
}