<?php

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
        $this->client = new SoapClient($this->config['wsdl'], [
            'trace' => 1,
            'exceptions' => true,
            'stream_context' => stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ])
        ]);
    }

    // Método para verificar disponibilidad del servicio
    public function ping()
    {
        try {
            $response = $this->client->Ping();
            return ['success' => true, 'response' => $response];
        } catch (\SoapFault $e) {
            $this->logError('ping', $e);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // Procesar venta
   // app/Services/PaymentGatewayService.php
    public function sale($token, $amount, $currency = 'USD', $reference = null)
    {
        try {
            $requestData = [
                'APIKey' => $this->config['api_key'],
                'accountToken' => $token,
                'accessCode' => $this->config['access_code'], // ← NUEVO CAMPO
                'merchantAccountNumber' => $this->config['merchant_id'],
                'terminalName' => $this->config['terminal_id'],
                'clientTracking' => $reference ?? uniqid('sale_'),
                'amount' => $amount,
                'currencyCode' => $currency
                // Agrega otros campos según necesites
            ];

            $this->logRequest('sale', $requestData);

            $response = $this->client->Sale($requestData);
            $responseData = json_decode(json_encode($response), true);

            $this->logResponse('sale', $responseData);

            return $responseData;

        } catch (\SoapFault $e) {
            $this->logError('sale', $e);
            throw new \Exception("Error processing sale: " . $e->getMessage());
        }
    }

    // Procesar transacción recurrente
    public function rebill($token, $amount, $currency = 'USD', $reference = null)
    {
        try {
            $requestData = [
                'apiKey' => $this->config['api_key'],
                'token' => $token,
                'amount' => $amount,
                'currency' => $currency,
                'reference' => $reference ?? uniqid('rebill_')
            ];

            $this->logRequest('rebill', $requestData);

            $response = $this->client->Rebill($requestData);
            $responseData = json_decode(json_encode($response), true);

            $this->logResponse('rebill', $responseData);

            return $responseData;

        } catch (\SoapFault $e) {
            $this->logError('rebill', $e);
            throw new \Exception("Error processing rebill: " . $e->getMessage());
        }
    }

    // Anular transacción
    public function void($transactionId, $reference = null)
    {
        try {
            $requestData = [
                'apiKey' => $this->config['api_key'],
                'transactionId' => $transactionId,
                'reference' => $reference ?? uniqid('void_')
            ];

            $this->logRequest('void', $requestData);

            $response = $this->client->Void($requestData);
            $responseData = json_decode(json_encode($response), true);

            $this->logResponse('void', $responseData);

            return $responseData;

        } catch (\SoapFault $e) {
            $this->logError('void', $e);
            throw new \Exception("Error voiding transaction: " . $e->getMessage());
        }
    }

    // Obtener detalles del token
    public function getTokenDetails($token)
    {
        try {
            $requestData = [
                'apiKey' => $this->config['api_key'],
                'token' => $token
            ];

            $this->logRequest('get_token_details', $requestData);

            $response = $this->client->GetTokenDetails($requestData);
            $responseData = json_decode(json_encode($response), true);

            $this->logResponse('get_token_details', $responseData);

            return $responseData;

        } catch (\SoapFault $e) {
            $this->logError('get_token_details', $e);
            throw new \Exception("Error getting token details: " . $e->getMessage());
        }
    }

    // Actualizar token
    public function updateToken($token, $expiryDate, $cardholderName)
    {
        try {
            $requestData = [
                'apiKey' => $this->config['api_key'],
                'token' => $token,
                'expiryDate' => $expiryDate,
                'cardholderName' => $cardholderName
            ];

            $this->logRequest('update_token', $requestData);

            $response = $this->client->UpdateToken($requestData);
            $responseData = json_decode(json_encode($response), true);

            $this->logResponse('update_token', $responseData);

            return $responseData;

        } catch (\SoapFault $e) {
            $this->logError('update_token', $e);
            throw new \Exception("Error updating token: " . $e->getMessage());
        }
    }

    // Obtener resultado de transacción
    public function getTransactionResult($transactionId, $trackingNumber)
    {
        try {
            $requestData = [
                'apiKey' => $this->config['api_key'],
                'transactionId' => $transactionId,
                'trackingNumber' => $trackingNumber
            ];

            $this->logRequest('get_transaction_result', $requestData);

            $response = $this->client->GetTransactionResult($requestData);
            $responseData = json_decode(json_encode($response), true);

            $this->logResponse('get_transaction_result', $responseData);

            return $responseData;

        } catch (\SoapFault $e) {
            $this->logError('get_transaction_result', $e);
            throw new \Exception("Error getting transaction result: " . $e->getMessage());
        }
    }

    // Métodos de logging (mantener igual)
    private function logRequest($type, $data) { /* ... */ }
    private function logResponse($type, $data) { /* ... */ }
    private function logError($type, \Exception $e) { /* ... */ }
}