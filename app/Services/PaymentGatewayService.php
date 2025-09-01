<?php
// app/Services/PaymentGatewayService.php

namespace App\Services;

use SoapClient;
use Illuminate\Support\Facades\Log;
use App\Models\PaymentTransaction;
use App\Models\PaymentLog;

class PaymentGatewayService
{
    private $client;
    private $config;

    public function __construct()
    {
        $this->config = config('payment');
        $this->initializeSoapClient();
    }

    private function initializeSoapClient()
    {
        try {
            $context = stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ]);

            $this->client = new SoapClient($this->config['wsdl'], [
                'trace' => 1,
                'exceptions' => true,
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'connection_timeout' => 30,
                'soap_version' => SOAP_1_2,
            ]);

        } catch (\SoapFault $e) {
            Log::error('Error inicializando SOAP client: ' . $e->getMessage());
            $this->client = null;
        }
    }

    /**
     * Procesar una transacción de venta
     */
    public function sale(array $data)
    {
        if (!$this->client) {
            return $this->simulateResponse($data);
        }
    
        try {
            $requestData = $this->prepareSaleRequest($data);
            $this->logRequest('sale', $requestData);
    

           $response    = $this->callWithRetry('Sale', $requestData, 2);
           $arr         = json_decode(json_encode($response), true);
           // 👇 A veces el SOAP envuelve la data en SaleResult
           $payload     = $arr['SaleResult'] ?? $arr;
    
           // Loguea ya “plano” para que sea legible
           $this->logResponse('sale', $payload);
           return $this->formatResponse($payload);
    
        } catch (\SoapFault $e) {
            $this->logError('sale', $e);
            return [
                'success' => false,
                'error' => 'Error SOAP: ' . $e->getMessage(),
                'transaction_id' => null,
                'authorization_number' => null,
                'response_code' => 'SOAP_ERROR',
    +           'response_description' => null,
            ];
        }
    }
    

    /**
     * Preparar datos para la petición Sale
     */
    private function prepareSaleRequest(array $data)
    {
        $baseRequest = [
            'APIKey' => $this->config['api_key'],
            'accountToken' => $data['token'],
            'accessCode' => $this->config['access_code'],
            'merchantAccountNumber' => $this->config['merchant_id'],
            'terminalName' => $this->config['terminal_id'],
            'clientTracking' => $data['reference'] ?? uniqid('sale_'),
            'amount' => (float) $data['amount'],
            'currencyCode' => $this->mapCurrency($data['currency'] ?? '840'),
            'emailAddress' => $data['email'] ?? '',
            'cvv' => $data['cvv'] ?? '',
            'systemTracking' => $data['reference'] ?? uniqid('sys_'),
            
            // Campos de billing REQUERIDOS por el WSDL
            'billingAddress' => $data['billing_address'] ?? 'Not Provided',
            'billingCity' => $data['billing_city'] ?? 'Not Provided',
            'billingState' => $data['billing_state'] ?? 'PA',
            'billingCountry' => $data['billing_country'] ?? 'PA',
            'billingZipCode' => $data['billing_zip'] ?? '0000',
            'billingPhoneNumber' => $data['billing_phone'] ?? '000-000-0000'
        ];

          // Define aquí los opcionales que tu WSDL acepte (ejemplos)
          $optionalFields = [
             'ipAddress', 'userAgent', 'customerId',
             'orderId', 'description', 'installments'
          ];

        foreach ($optionalFields as $field) {
            if (!empty($data[$field])) {
                $baseRequest[$field] = $data[$field];
            }
        }

        // Item details si están presentes
        if (!empty($data['items'])) {
            $baseRequest['itemDetails'] = $this->prepareItemDetails($data['items']);
        }

        // Additional data si está presente
        if (!empty($data['additional_data'])) {
            $baseRequest['additionalData'] = $this->prepareAdditionalData($data['additional_data']);
        }

        return $baseRequest;
    }


     private function mapCurrency($value): string
    {
    // Si ya viene numérico (p.ej., "840"), lo usamos
    if (preg_match('/^\d{3}$/', (string)$value)) {
        return (string)$value;
    }
    // Mapeo básico - agrega lo que necesites
    $map = [
        'USD' => '840',
        'EUR' => '978',
        'PAB' => '590',
    ];
    return $map[strtoupper((string)$value)] ?? '840';
    }

    private function prepareItemDetails(array $items)
    {
        $itemDetails = [];
        foreach ($items as $item) {
            $itemDetails[] = [
                'code' => $item['code'] ?? '',
                'name' => $item['name'] ?? '',
                'description' => $item['description'] ?? '',
                'quantity' => (int) ($item['quantity'] ?? 1),
                'unitPrice' => (float) ($item['unit_price'] ?? 0)
            ];
        }
        return $itemDetails;
    }

    private function prepareAdditionalData(array $additionalData)
    {
        $data = [];
        foreach ($additionalData as $key => $value) {
            $data[] = [
                'name' => $key,
                'value' => $value
            ];
        }
        return $data;
    }

    // private function formatResponse(array $r)
    // {
    //     // Normaliza mayúsculas/minúsculas para claves comunes
    //     $code = $r['Code'] ?? $r['code'] ?? null;
    //     $res  = $r['Result'] ?? $r['result'] ?? null;
    //     $desc = $r['Description'] ?? $r['description'] ?? null;
    //     $txid = $r['TransactionId'] ?? $r['transactionId'] ?? $r['transaction_id'] ?? null;
    //     $auth = $r['AuthorizationNumber'] ?? $r['authorizationNumber'] ?? $r['authorization_number'];


    //     $isApproved = ($code === '00') && (strtolower((string)$res) === 'approved');
        
    //     return [
    //    'success' => $isApproved,
    //    'transaction_id' => $txid,
    //    'authorization_number' => $auth,
    //    'response_code' => $code ?? 'UNKNOWN',
    //    'response_description' => $desc ?? '',
    //    'result' => $res,
    //    'bin_id' => $r['BinId'] ?? $r['binId'] ?? null,
    //    'processor_id' => $r['ProcessorId'] ?? $r['processorId'] ?? null,
    //    'tracking' => $r['Tracking'] ?? $r['tracking'] ?? null,
    //    'system_tracking' => $r['SystemTracking'] ?? $r['systemTracking'] ?? null,
    //    'request_date' => $r['RequestDate'] ?? $r['requestDate'] ?? null,
    //    'response_date' => $r['ResponseDate'] ?? $r['responseDate'] ?? null,
    //    'raw_response' => $r,
    //     ];
    // }

    private function formatResponse(array $r)
    {
        // Si todavía viene envuelto por cualquier razón, desenvuelve defensivo
        if (isset($r['SaleResult']) && is_array($r['SaleResult'])) {
            $r = $r['SaleResult'];
        }

        // Mapea claves con variantes
        $code = $r['Code'] 
            ?? $r['ResponseCode'] 
            ?? $r['code'] 
            ?? $r['responseCode'] 
            ?? null;

        $res  = $r['Result'] 
            ?? $r['result'] 
            ?? null;

        $desc = $r['Description'] 
            ?? $r['ResponseDescription'] 
            ?? $r['description'] 
            ?? null;

        $txid = $r['TransactionId'] 
            ?? $r['TransactionID'] 
            ?? $r['transactionId'] 
            ?? $r['transaction_id'] 
            ?? null;

        $auth = $r['AuthorizationNumber'] 
            ?? $r['AuthorizationCode'] 
            ?? $r['authorizationNumber'] 
            ?? $r['authorization_code'] 
            ?? $r['authCode'] 
            ?? null;

        // Aprobación: acepta Code '00' o Result 'Approved'
        $isApproved =
            ($code === '00') ||
            (is_string($res)  && strcasecmp($res,  'approved') === 0) ||
            (is_string($desc) && strcasecmp($desc, 'approved') === 0);

        return [
            'success'               => (bool) $isApproved,
            'transaction_id'        => $txid,
            'authorization_number'  => $auth,
            'response_code'         => $code ?? 'UNKNOWN',
            'response_description'  => $desc ?? '',
            'result'                => $res ?? null,
            'bin_id'                => $r['BinId'] ?? $r['binId'] ?? null,
            'processor_id'          => $r['ProcessorId'] ?? $r['processorId'] ?? null,
            'tracking'              => $r['Tracking'] ?? $r['tracking'] ?? null,
            'system_tracking'       => $r['SystemTracking'] ?? $r['systemTracking'] ?? null,
            'request_date'          => $r['RequestDate'] ?? $r['requestDate'] ?? null,
            'response_date'         => $r['ResponseDate'] ?? $r['responseDate'] ?? null,
            'raw_response'          => $r,
        ];
    }

   

    private function simulateResponse(array $data)
    {
        // Simulación para desarrollo
        return [
            'success' => true,
            'transaction_id' => 'SIM_' . uniqid(),
            'authorization_number' => 'SIM' . rand(100000, 999999),
            'response_code' => '00',
            'response_description' => 'Transacción simulada exitosamente',
            'bin_id' => 'SIM',
            'processor_id' => 999,
            'internal_response_code' => 200,
            'tracking' => $data['reference'] ?? null,
            'request_date' => now()->toDateTimeString(),
            'response_date' => now()->toDateTimeString(),
            'raw_response' => ['simulated' => true]
        ];
    }

    private function logRequest($type, $data)
    {
        PaymentLog::create([
            'type' => 'request',
            'data' => json_encode($this->sanitizeData($data)),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    private function logResponse($type, $data)
    {
        PaymentLog::create([
            'type' => 'response',
            'data' => json_encode($data),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    private function logError($type, \Exception $e)
    {
        PaymentLog::create([
            'type' => 'error',
            'data' => json_encode([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    private function sanitizeData($data)
    {
        // Eliminar datos sensibles para logs
        $sensitiveFields = ['APIKey', 'accessCode', 'accountToken', 'cvv'];
        
        foreach ($sensitiveFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = '***REDACTED***';
            }
        }
        
        return $data;
    }

    // Método Ping para verificar conexión
    public function ping()
    {
        if (!$this->client) {
            return ['success' => false, 'error' => 'SOAP client no inicializado'];
        }

        try {
            $response = $this->client->Ping();
            return ['success' => true, 'response' => $response];
        } catch (\SoapFault $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function getServiceInfo()
    {
        if (!$this->client) return ['error' => 'SOAP client no inicializado'];
        try {
            return [
                'functions' => $this->client->__getFunctions(), // ← revisa aquí
                'types' => $this->client->__getTypes(),
            ];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    

    public function testSale()
    {
        if (!$this->client) {
            $this->initializeSoapClient();
        }

        try {
            $testData = [
                'token' => '46622023-07c9-4a76-9db2-70ceab3b637c', // Tu token de prueba
                'amount' => 1.00,
                'currency' => 'USD',
                'email' => 'test@example.com',
                'reference' => 'TEST_' . uniqid(),
                'billing_address' => 'Calle Test 123',
                'billing_city' => 'Panama City',
                'billing_state' => 'PA',
                'billing_country' => 'PA',
                'billing_zip' => '0801',
                'billing_phone' => '600-000-0000'
            ];

            return $this->sale($testData);

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    private function callWithRetry(string $method, array $payload, int $retries = 2)
    {
        attempt:
        try {
            return $this->client->__soapCall($method, [$payload]);
        } catch (\SoapFault $e) {
            if ($retries-- > 0 && $this->isTransientFault($e)) {
                usleep(200000); // 200ms
                goto attempt;
            }
            throw $e;
        }
    }

    private function isTransientFault(\SoapFault $e): bool
    {
        $msg = strtolower($e->getMessage());
        return str_contains($msg, 'timeout') || str_contains($msg, 'temporarily') || str_contains($msg, 'unavailable');
    }

}