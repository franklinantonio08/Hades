<?php

namespace App\Http\Controllers\Dist;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\NeoPaymentTokenService;
use App\Models\ConfigPayment;
use App\Models\PaymentTransaction;
use App\Models\PaymentLog;
use App\Models\PaymentEmailLog;
use App\Models\PaymentToken;

use App\Helpers\CommonHelper;
use Illuminate\Http\Request;


class NeoPaymentController extends Controller
{
    protected $request;
    protected $common;
    protected string $host;

    public function __construct(Request $request, CommonHelper $common)
    {
        $this->request = $request;
        $this->common  = $common;

        // Host desde BD
        $this->host = rtrim(
            ConfigPayment::where('tipo', 'host')
                ->where('estatus', 'Activo')
                ->value('descripcion'),
            '/'
        );


    }

    public function process(){

    
   
        try {

            DB::beginTransaction();

                $solicitudId = $this->request->solicitud_id;

                $amount = (int) $this->request->amount * 100;

                $percentFee = round($amount * 0.0275); 
                $fixedFee = 35;                
                $transactionFee = $percentFee + $fixedFee;

                $reference = 'CR-' . $solicitudId . '-' . time();

                $totalAmount = $amount + $transactionFee ;

                $solicitud = DB::table('solicitudes_cambio_residencia')
                ->where('solicitudes_cambio_residencia.id', $solicitudId)
                ->leftjoin('solicitudes_cambio_personas', 'solicitudes_cambio_personas.solicitud_id', '=', 'solicitudes_cambio_residencia.id')
                ->select(
                    'solicitudes_cambio_residencia.*',
                    'solicitudes_cambio_personas.primer_nombre',
                    'solicitudes_cambio_personas.segundo_nombre',
                    'solicitudes_cambio_personas.primer_apellido',
                    'solicitudes_cambio_personas.segundo_apellido',
                    'solicitudes_cambio_personas.pasaporte',
                    'solicitudes_cambio_personas.correo',
                    'solicitudes_cambio_personas.telefono',
                    'solicitudes_cambio_personas.num_filiacion'
                    )
                ->first();

                if (!$solicitud) {
                    return back()->withErrors('Solicitud no encontrada.');
                }

                // $pagoPendiente = DB::table('pagos')
                //     ->where('solicitud_id', $solicitudId)
                //     ->whereIn('status', ['Pendiente', 'En proceso'])
                //     ->orderByDesc('id')
                //     ->first();

                // if ($pagoPendiente) {
                //     $paymentId = $pagoPendiente->id;

                //     // si cambió el monto por alguna razón, lo actualizas
                //     DB::table('pagos')->where('id', $paymentId)->update([
                //         'amount'     => $amount + $transactionFee,
                //         'currency'   => 'USD',
                //         'updated_at' => now(),
                //     ]);
                // } else {

                //         $paymentId = DB::table('pagos')->insertGetId([
                //         'solicitud_id' => $solicitudId,
                //         'amount'       => $amount + $transactionFee,
                //         'currency'     => 'USD',
                //         'status'       => 'Pendiente',
                //         'created_at'   => now(),
                //         'updated_at'   => now(),
                //     ]);
                // }

                //dd("ENTRO AL METODO PROCESS", $solicitud);

                if (($solicitud->estatus ?? '') === 'Aprobada - con pago') {
                    DB::table('solicitudes_cambio_residencia')
                        ->where('id', $solicitudId)
                        ->update(['estatus' => 'Pago en proceso']);
                }
                            
                $urlOk  = route('payment.success', ['solicitud_id' => $solicitudId]);
                $urlKo  = route('payment.error',   ['solicitud_id' => $solicitudId]);
                $webhook = route('payment.webhook');

                // $urlOk  = "https://8f3d-190-34-23-11.ngrok-free.app/payment/success?solicitud_id=/".$solicitudId;
                // $urlKo  = "https://8f3d-190-34-23-11.ngrok-free.app/payment/error?solicitud_id=/".$solicitudId;
                // $webhook = "https://8f3d-190-34-23-11.ngrok-free.app/payment/webhook";

                    $transaction = new PaymentTransaction();

                    $transaction->user_id = Auth::id();
                    $transaction->token_id = null;
                    $transaction->amount = $totalAmount;
                    $transaction->currency = 'USD';
                    $transaction->reference = $reference;
                    $transaction->ruex = $solicitud->num_filiacion ?? null;
                    $transaction->email = $solicitud->correo ?? null;
                    $transaction->request_date = now();
                    $transaction->status = 'Pendiente';
                    $transaction->id_solicitud = $solicitudId;
                    $transaction->codigo_solicitud = $reference;
                    //$transaction->request_data = json_encode($payload);

                    $transaction->save();
                //$transactionId = $transaction->id;

                $payload  = [

                    "currency_code" => "USD",

                    "checkout_items" => [
                        [
                            "name"     => "Cambio de Residencia",
                            "quantity" => 1,
                            "price"    => $amount,
                        ],
                        [
                            "name"     => "Cargo por transacción",
                            "quantity" => 1,
                            "price"    => $transactionFee,
                        ]
                    ],

                    "return_url" => $urlOk,
                    "url_ok"     => $urlOk,
                    "url_ko"     => $urlKo,
                    "webhook"    => $webhook,
                    "source" => config('app.url'),

                    
                    // "return_url" =>  "https://8f3d-190-34-23-11.ngrok-free.app/payment/error?solicitud_id=" . $solicitudId,
                    // "url_ok"      => "https://8f3d-190-34-23-11.ngrok-free.app/payment/success?solicitud_id=/".$solicitudId,
                    // "url_ko"      => "https://8f3d-190-34-23-11.ngrok-free.app/payment/error?solicitud_id=/".$solicitudId,
                    // "webhook"     => "https://8f3d-190-34-23-11.ngrok-free.app/payment/webhook",
                    // "source" => "https://8f3d-190-34-23-11.ngrok-free.app",

                    "metadatas" => [
                        // "payment_id" =>  (string)$solicitudId,
                        // "client_name" =>  (string)$solicitudId,
                        // "email" =>  (string)$solicitudId,
                        // "transaction_id" =>  (string)$solicitudId,
                        
                        // "solicitud_id"      => (string) $solicitudId,
                        // "pago_id"           => (string) $paymentId,
                        "payment_reference" =>  $reference,
                    ],

                    "customer" => [
                        "type"          => "natural", // o "legal_entity"
                        "name"          => trim(($solicitud->primer_nombre ?? '') . ' ' . ($solicitud->segundo_nombre ?? '')),
                        "first_surname" => trim(($solicitud->primer_apellido ?? '') . ' ' . ($solicitud->segundo_apellido ?? '')),
                        "doc_id_type"   => "P", // P=pasaporte, C=cédula (según doc)
                        "doc_id"        => (string) ($solicitud->pasaporte ?? ''),

                        "metadata" => [
                            "email" => (string) ($solicitud->correo ?? ''),
                            // "phone" => (string) ($solicitud->telefono ?? '0000-0000'),
                        ],
                    ],
                ];

                $transaction->request_data = json_encode($payload);
                $transaction->save();

                $redirectUrl = NeoPaymentTokenService::createCheckout($payload);
              

                // DB::table('payment_transactions')
                //     ->where('id', $transactionId)
                //     ->update([
                //         'status' => 'En proceso',
                //         'request_data' => json_encode($payload),
                //         'updated_at' => now(),
                //     ]);

                // DB::table('pagos')->where('id', $paymentId)->update([
                //     'status'     => 'En proceso',
                //     'updated_at' => now(),
                // ]);

                DB::commit();

                return redirect($redirectUrl);

        } catch (\Throwable $e) {

            DB::rollBack();

            return back()->withErrors('Error iniciando pago: ' . $e->getMessage());
        }
    }





    // public function webhook(Request $request)
    // {
    //     $payload = $request->all();

    //     DB::table('payment_logs')->insert([
    //         'type' => 'webhook',
    //         'data' => json_encode($payload),
    //         'ip_address' => $request->ip(),
    //         'user_agent' => $request->userAgent(),
    //         'created_at' => now(),
    //     ]);

    //     if (($payload['status'] ?? '') !== 'authorized') {
    //         return response()->json(['ok' => true]);
    //     }

    //     $solicitudId = $payload['metadatas']['solicitud_id'];

    //     DB::table('payment_transactions')
    //         ->where('id', $transactionId)
    //         ->update([
    //             'status' => $payload['status'],
    //             'gateway_transaction_id' => $payload['id'] ?? null,
    //             'authorization_number' => $payload['authorization_number'] ?? null,
    //             'response_code' => $payload['response_code'] ?? null,
    //             'response_data' => json_encode($payload),
    //             'response_date' => now(),
    //             'updated_at' => now(),
    //         ]);

    //     DB::table('solicitudes_cambio_residencia')
    //         ->where('id', $solicitudId)
    //         ->update(['estatus' => 'Pagada']);

    //     return response()->json(['ok' => true]);
    // }

    public function webhook(Request $request)
    {
        try {

            $payload = $request->all();

            /*
            |--------------------------------------------------------------------------
            | 1. Guardar LOG del webhook
            |--------------------------------------------------------------------------
            */

            $log = new PaymentLog();
            $log->type = 'webhook';
            $log->data = json_encode($payload);
            $log->ip_address = $request->ip();
            $log->user_agent = $request->userAgent();
            $log->save();


            /*
            |--------------------------------------------------------------------------
            | 2. Validar estado
            |--------------------------------------------------------------------------
            */

            $status = $payload['status'] ?? null;

            if (!$status) {
                return response()->json(['ok' => true]);
            }


            /*
            |--------------------------------------------------------------------------
            | 3. Obtener referencia enviada al checkout
            |--------------------------------------------------------------------------
            */

            $paymentReference = $payload['metadatas']['payment_reference'] ?? null;

            if (!$paymentReference) {
                return response()->json(['ok' => true]);
            }


            /*
            |--------------------------------------------------------------------------
            | 4. Buscar transacción local
            |--------------------------------------------------------------------------
            */

            $transaction = PaymentTransaction::where('reference', $paymentReference)->first();

            if (!$transaction) {
                return response()->json(['ok' => true]);
            }


            /*
            |--------------------------------------------------------------------------
            | 5. Evitar doble procesamiento
            |--------------------------------------------------------------------------
            */

            if ($transaction->status === 'authorized') {
                return response()->json(['ok' => true]);
            }


            /*
            |--------------------------------------------------------------------------
            | 6. Actualizar transacción
            |--------------------------------------------------------------------------
            */

            $transaction->status = $status;
            $transaction->gateway_transaction_id = $payload['id'] ?? null;
            $transaction->authorization_number = $payload['authorization_number'] ?? null;
            $transaction->response_code = $payload['response_code'] ?? null;
            $transaction->response_data = json_encode($payload);
            $transaction->response_date = now();

            $transaction->save();


            /*
            |--------------------------------------------------------------------------
            | 7. Si el pago fue autorizado
            |--------------------------------------------------------------------------
            */

            if ($status === 'authorized') {

                DB::table('solicitudes_cambio_residencia')
                    ->where('id', $transaction->id_solicitud)
                    ->update([
                        'estatus' => 'Pagada'
                    ]);

            }


            return response()->json(['ok' => true]);

        } catch (\Throwable $e) {

            $log = new PaymentLog();
            $log->type = 'webhook_error';
            $log->data = $e->getMessage();
            $log->ip_address = $request->ip();
            $log->user_agent = $request->userAgent();
            $log->save();

            return response()->json(['ok' => false]);

        }
    }



    public function success(Request $request)
    {
        $solicitudId = (int) $request->query('solicitud_id');

        // DB::table('payment_transactions')
        //     ->where('id_solicitud', $solicitudId)
        //     ->update(['status' => 'authorized']);
        

        return redirect()->route('solicitud.PagoCompletado', $solicitudId);
    }

    public function error(Request $request)
    {

        $solicitudId = (int) $request->query('solicitud_id');

        return redirect()->route('solicitud.PagoRechazado', $solicitudId);
    }



}
