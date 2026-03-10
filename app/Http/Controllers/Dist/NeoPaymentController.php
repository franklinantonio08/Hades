<?php

namespace App\Http\Controllers\Dist;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\NeoPaymentTokenService;
use App\Models\ConfigPayment;
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
                    'solicitudes_cambio_personas.telefono'
                    )
                ->first();

                

                // return $solicitud->primer_nombre;

                if (!$solicitud) {
                    return back()->withErrors('Solicitud no encontrada.');
                }

                $transactionId = DB::table('payment_transactions')->insertGetId([
                    'user_id' => Auth::id(),
                    'token_id' => 1, // ⚠ si no usas tokens aún, debes permitir NULL en BD
                    'amount' => $totalAmount,
                    'currency' => 'USD',
                    'reference' => $reference,
                    'ruex' => $solicitud->num_filiacion ?? null,
                    'email' => $solicitud->correo ?? null,
                    'request_date' => now(),
                    'status' => 'Pendiente',
                    'id_solicitud' => $solicitudId,
                    'codigo_solicitud' => $reference,
                    'request_data' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

               

                if (($solicitud->estatus ?? '') === 'Aprobada - con pago') {
                    DB::table('solicitudes_cambio_residencia')
                        ->where('id', $solicitudId)
                        ->update(['estatus' => 'Pago en proceso']);
                }
              
                // $urlOk  = route('payment.success', ['id' => $solicitudId], true);
                // $urlKo  = route('payment.error', ['id' => $solicitudId], true);
                // $webhook = route('payment.webhook', [], true);

                $urlOk  = "https://8f3d-190-34-23-11.ngrok-free.app/payment/success?solicitud_id=/".$solicitudId;
                $urlKo  = "https://8f3d-190-34-23-11.ngrok-free.app/payment/error?solicitud_id=/".$solicitudId;
                $webhook = "https://8f3d-190-34-23-11.ngrok-free.app/payment/webhook";

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

                    "metadatas" => [
                        "payment_reference" =>  $reference,
                    ],

                    "customer" => [
                        "type"          => "natural", 
                        "name"          => trim(($solicitud->primer_nombre ?? '') . ' ' . ($solicitud->segundo_nombre ?? '')),
                        "first_surname" => trim(($solicitud->primer_apellido ?? '') . ' ' . ($solicitud->segundo_apellido ?? '')),
                        "doc_id_type"   => "P", 
                        "doc_id"        => (string) ($solicitud->pasaporte ?? ''),

                        "metadata" => [
                            "email" => (string) ($solicitud->correo ?? ''),
                            "phone" => (string) ($solicitud->telefono ?? '60000000')
                        ],
                    ],
                ];

                $redirectUrl = NeoPaymentTokenService::createCheckout($payload);

                

                DB::table('payment_transactions')
                    ->where('id', $transactionId)
                    ->update([
                        'status' => 'En proceso',
                        'request_data' => json_encode($payload),
                        'updated_at' => now(),
                    ]);

                // DB::table('pagos')->where('id', $paymentId)->update([
                //     'status'     => 'En proceso',
                //     'updated_at' => now(),
                // ]);

                DB::commit();

                return redirect()->away($redirectUrl);

        } catch (\Throwable $e) {

            DB::rollBack();

            return back()->withErrors('Error iniciando pago: ' . $e->getMessage());
        }
    }

    public function webhook(Request $request){

        $payload = $request->all();

        DB::table('payment_logs')->insert([
            'type' => 'webhook',
            'data' => json_encode($payload),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        if (($payload['status'] ?? '') !== 'authorized') {
            return response()->json(['ok' => true]);
        }

        $reference = $payload['metadatas']['payment_reference'] ?? null;

        if (!$reference) {
            return response()->json(['ok' => false], 400);
        }

        $transaction = DB::table('payment_transactions')
            ->where('reference', $reference)
            ->first();

        if (!$transaction) {
            return response()->json(['ok' => false], 404);
        }

        DB::table('payment_transactions')
            ->where('id', $transaction->id)
            ->update([
                'status' => $payload['status'],
                'gateway_transaction_id' => $payload['id'] ?? null,
                'authorization_number' => $payload['authorization_number'] ?? null,
                'response_code' => $payload['response_code'] ?? null,
                'response_data' => json_encode($payload),
                'response_date' => now(),
                'updated_at' => now(),
            ]);

        DB::table('solicitudes_cambio_residencia')
            ->where('id', $transaction->id_solicitud)
            ->update([
                'estatus' => 'Pagada'
            ]);

        return response()->json(['ok' => true]);
    }

    public function success(Request $request, $id){

        $solicitudId = $id;

        return redirect()->route('solicitud.PagoCompletado', $solicitudId);
    }

    public function error(Request $request, $id){

        $solicitudId = $id;

        return redirect()->route('solicitud.PagoRechazado', $solicitudId);
    }

}
