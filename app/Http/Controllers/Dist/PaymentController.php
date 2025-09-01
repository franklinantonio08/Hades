<?php
// app/Http/Controllers/Dist/PaymentController.php
// namespace App\Http\Controllers\Dist;

// use App\Http\Controllers\Controller;
// use App\Services\PaymentGatewayService;
// use App\Models\PaymentToken;
// use App\Models\PaymentTransaction;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Validator;

// class PaymentController extends Controller
// {
//     protected $paymentService;

//     public function __construct(PaymentGatewayService $paymentService)
//     {
//         $this->paymentService = $paymentService;
//     }

//     // Mostrar formulario con Widget
//     public function showTokenizationForm()
//     {
//         //Depuración temporal
//         \Log::info('Accediendo a showTokenizationForm');
//         \Log::info('Usuario autenticado: ' . (auth()->check() ? 'Sí' : 'No'));
//         \Log::info('User ID: ' . (auth()->check() ? auth()->id() : 'N/A'));
        
//         if (!auth()->check()) {
//             \Log::warning('Usuario no autenticado, redirigiendo a login');
//         }
        
//         return view('payment.tokenize', [
//             'api_key' => config('payment.api_key'),
//             'layout' => 'layouts.payment' // Usar layout de pagos
//         ]);
//        // Vista mínima sin layout
//     //    return response()->view('payment.minimal-tokenize', [
//     //     'api_key' => config('payment.api_key')
//     // ]);
    
//     }

//     // Procesar callback del Widget
//     public function handleWidgetCallback(Request $request)
//     {
//         $validator = Validator::make($request->all(), [
//             'token' => 'required|string',
//             'account_number' => 'required|string',
//             'cardholder_name' => 'required|string',
//             'last_four' => 'required|digits:4',
//             'brand' => 'required|string',
//             'expiry_date' => 'nullable|date_format:Y-m'
//         ]);

//         if ($validator->fails()) {
//             return response()->json([
//                 'success' => false,
//                 'errors' => $validator->errors()
//             ], 422);
//         }

//         try {
//             // Verificar si el token ya existe
//             $existingToken = PaymentToken::where('token', $request->token)
//                                         ->where('user_id', auth()->id())
//                                         ->first();

//             if ($existingToken) {
//                 return response()->json([
//                     'success' => true,
//                     'message' => 'Token ya existente',
//                     'token' => $existingToken
//                 ]);
//             }

//             // Guardar token en base de datos
//             $token = PaymentToken::create([
//                 'user_id' => auth()->id(),
//                 'token' => $request->token,
//                 'account_number' => $request->account_number,
//                 'last_four_digits' => $request->last_four,
//                 'brand' => $request->brand,
//                 'cardholder_name' => $request->cardholder_name,
//                 'expiry_date' => $request->expiry_date ? $request->expiry_date . '-01' : null
//             ]);

//             return response()->json([
//                 'success' => true,
//                 'message' => 'Tarjeta guardada exitosamente',
//                 'token' => $token
//             ]);

//         } catch (\Exception $e) {
//             \Log::error('Error en widget callback: ' . $e->getMessage());
            
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Error al guardar la tarjeta: ' . $e->getMessage()
//             ], 500);
//         }
//     }

//     // Procesar pago con token
//     public function processPayment(Request $request)
//     {
//         $validator = Validator::make($request->all(), [
//             'token' => 'required|string',
//             'account_number' => 'required|string',
//             'amount' => 'required|numeric|min:0.01',
//             'currency' => 'required|string|size:3'
//         ]);

//         if ($validator->fails()) {
//             return response()->json([
//                 'success' => false,
//                 'errors' => $validator->errors()
//             ], 422);
//         }

//         try {
//             // Verificar que el token pertenece al usuario
//             $token = PaymentToken::where('token', $request->token)
//                                 ->where('user_id', auth()->id())
//                                 ->firstOrFail();

//             // Procesar la transacción
//             $response = $this->paymentService->sale(
//                 $request->token,
//                 $request->amount,
//                 $request->currency,
//                 uniqid('txn_')
//             );

//             // Guardar transacción
//             $transaction = PaymentTransaction::create([
//                 'user_id' => auth()->id(),
//                 'token_id' => $token->id,
//                 'amount' => $request->amount,
//                 'currency' => $request->currency,
//                 'reference' => $response['reference'] ?? uniqid('txn_'),
//                 'status' => $response['status'] ?? 'completed',
//                 'request_data' => $request->all(),
//                 'response_data' => $response,
//                 'gateway_transaction_id' => $response['transactionId'] ?? null
//             ]);

//             return response()->json([
//                 'success' => true,
//                 'message' => 'Pago procesado exitosamente',
//                 'transaction' => $transaction
//             ]);

//         } catch (\Exception $e) {
//             \Log::error('Error procesando pago: ' . $e->getMessage());
            
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Error al procesar el pago: ' . $e->getMessage()
//             ], 500);
//         }
//     }
// }


// app/Http/Controllers/Dist/PaymentController.php
namespace App\Http\Controllers\Dist;

use App\Http\Controllers\Controller;
use App\Services\PaymentGatewayService;
use App\Models\PaymentToken;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

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
        Log::info('Accediendo a showTokenizationForm');
        Log::info('Usuario autenticado: ' . (auth()->check() ? 'Sí' : 'No'));
        Log::info('User ID: ' . (auth()->check() ? auth()->id() : 'N/A'));
        
        if (!auth()->check()) {
            Log::warning('Usuario no autenticado, redirigiendo a login');
        }
        
        return view('payment.tokenize', [
            'api_key' => config('payment.api_key'),
            'layout' => 'layouts.payment'
        ]);
    }

    // Procesar callback del Widget
    public function handleWidgetCallback(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'account_number' => 'required|string',
            'cardholder_name' => 'required|string',
            'last_four' => 'required|digits:4',
            //'brand' => 'required|string',
            //'expiry_date' => 'nullable|date_format:Y-m'
            'brand' => 'nullable|string',
            'expiry_date' => 'nullable|string' // la normalizamos abajo si llega
            
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Normalizar expiry_date si vino tipo "MM/YY" o "MMYYYY"
        if ($request->filled('expiry_date')) {
            $raw = preg_replace('/\s+/', '', $request->input('expiry_date'));
            if (preg_match('/^(\d{2})[\/\-]?(\d{2,4})$/', $raw, $m)) {
                $mm = $m[1];
                $yyyy = strlen($m[2]) === 2 ? ('20'.$m[2]) : $m[2];
                $request->merge(['expiry_date' => "$yyyy-$mm"]);
            } else {
                // si viene con formato inesperado, mejor lo vaciamos para no romper
                $request->merge(['expiry_date' => null]);
            }
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
            Log::error('Error en widget callback: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la tarjeta: ' . $e->getMessage()
            ], 500);
        }
    }

    // Procesar pago con token - VERSIÓN ACTUALIZADA
    public function processPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token'       => 'required|string',
         //   'account_number' => 'required|string',
            'amount'      => 'required|numeric|min:0.01',
            'currency'    => 'required|in:840',
            'ruex'        => 'required|string',
            'full_name'   => 'required|string',
            'email'       => 'required|email',
            'cvv'         => 'required|digits:3',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
        $query = PaymentToken::where('token', $request->token);
        if (auth()->check()) {
            $query->where('user_id', auth()->id());
            }
        $token = $query->firstOrFail();

            // Preparar datos para el pago
            $paymentData = [
                'token' => $request->token,
                'amount' => $request->amount,
                'currency' => $request->currency,
                'email' => $request->email,
                'reference' => 'PAY_' . uniqid(),
                'ruex' => $request->ruex,
                'full_name' => $request->full_name,
                'cvv' => $request->cvv,
                       // Opcionales útiles (tu servicio los toma si existen)
                'ipAddress' => $request->ip(),
                'userAgent' => $request->userAgent(),
                // Campos de billing requeridos
                'billing_address' => 'Not Provided', // Puedes obtener estos de perfil usuario
                'billing_city' => 'Not Provided',
                'billing_state' => 'PA',
                'billing_country' => 'PA',
                'billing_zip' => '0000',
                'billing_phone' => '000-000-0000'
            ];

            // Procesar la transacción
            $response = $this->paymentService->sale($paymentData);

            // Guardar transacción
            $transaction = PaymentTransaction::create([
                'user_id'                => auth()->id(),
                'token_id'               => $token->id,
                'amount'                 => $request->amount,
                'currency'               => $request->currency,
                'reference'              => $paymentData['reference'],
                'status'                 => $response['success'] ? 'completed' : 'failed',
                'authorization_code'     => $response['authorization_number'] ?? $response['authCode'] ?? null,
                'gateway_transaction_id' => $response['transaction_id'] ?? $response['id'] ?? null,
                'request_data'           => $this->sanitizeRequestData($request->all()),
                'response_data'          => $response,
            ]);

            return response()->json([
                'success' => (bool)$response['success'],
                'message' => $response['success']
                    ? 'Pago procesado exitosamente'
                    : ($response['response_description'] ?? $response['description'] ?? $response['error'] ?? 'Error de la pasarela'),
                'transaction' => [
                    'id'                     => $transaction->id,
                    'authorization_code'     => $transaction->authorization_code,
                    'gateway_transaction_id' => $transaction->gateway_transaction_id,
                ],
                'response' => $response,
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token no encontrado o no pertenece al usuario actual.',
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Error procesando pago: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el pago: ' . $e->getMessage(),
            ], 500);
        }
    }

    // Método para sanitizar datos sensibles en el log
    private function sanitizeRequestData($data)
    {
        // Eliminar datos sensibles del log
        unset($data['token'], $data['cvv'], $data['account_number']);
        return $data;
    }

    // Método para éxito de pago (opcional)
    public function paymentSuccess(Request $request)
    {
        $transactionId = $request->query('transaction');
        
        $transaction = PaymentTransaction::where('id', $transactionId)
                                        ->where('user_id', auth()->id())
                                        ->first();

        return view('payment.success', [
            'transaction' => $transaction,
            'layout' => 'layouts.payment'
        ]);
    }

    // Método para verificar estado del servicio (opcional)
    public function checkServiceStatus()
    {
        try {
            $status = $this->paymentService->ping();
            
            return response()->json([
                'success' => $status['success'],
                'status' => $status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking service status: ' . $e->getMessage()
            ], 500);
        }
    }

    
}