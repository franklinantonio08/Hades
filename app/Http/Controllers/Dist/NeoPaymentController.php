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

public function process()
{
        $solicitudId = $this->request->solicitud_id;
        $amount      = '100';

        $payload  = [

            "currency_code" => "USD",

            "checkout_items" => [
                [
                    "name"     => "Cambio de Residencia",
                    "quantity" => 1,
                    "price"    => $amount,
                ]
            ],

            
            "return_url" =>  "https://8f3d-190-34-23-11.ngrok-free.app/payment/error?solicitud_id=" . $solicitudId,
            "url_ok"      => "https://8f3d-190-34-23-11.ngrok-free.app/payment/success?solicitud_id=/".$solicitudId,
            "url_ko"      => "https://8f3d-190-34-23-11.ngrok-free.app/payment/error?solicitud_id=/".$solicitudId,
            "webhook"     => "https://8f3d-190-34-23-11.ngrok-free.app/payment/webhook",
            "source" => "https://8f3d-190-34-23-11.ngrok-free.app",

            "metadatas" => [
                "payment_id" =>  (string)$solicitudId,
                "client_name" =>  (string)$solicitudId,
                "email" =>  (string)$solicitudId,
                "transaction_id" =>  (string)$solicitudId,
            ]  
        ];

        $redirectUrl = NeoPaymentTokenService::createCheckout($payload);

        return redirect($redirectUrl);
}





public function webhook(Request $request)
{
    $payload = $request->all();

    if (($payload['status'] ?? '') !== 'authorized') {
        return response()->json(['ok' => true]);
    }

    $solicitudId = $payload['metadatas']['solicitud_id'];

    DB::table('pagos')->insert([
        'solicitud_id'   => $solicitudId,
        'transaction_id' => $payload['id'],
        'status'         => $payload['status'],
        'authorization_number' => $payload['authorization_number'] ?? null,
        'response_code'  => $payload['response_code'] ?? null,
        'card_brand'     => $payload['metadatas']['card_brand'] ?? null,
        'pan_masked'     => $payload['pan'] ?? null,
        'amount'         => $payload['amount'] / 100,
        'currency'       => $payload['currency'],
        'raw_response'   => json_encode($payload),
        'created_at'     => now(),
    ]);

    DB::table('solicitudes_cambio_residencia')
        ->where('id', $solicitudId)
        ->update(['estatus' => 'Pagada']);

    return response()->json(['ok' => true]);
}



public function success($id)
{
    return redirect()->route('solicitud.Mostrar', $id)
        ->with('success', 'Pago realizado correctamente.');
}

public function error($id)
{
    return redirect()->route('solicitud.Mostrar', $id)
        ->withErrors('El pago fue rechazado.');
}



}
