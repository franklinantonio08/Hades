<?php
// app/Console/Commands/TestPaymentConnection.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use SoapClient;

class TestPaymentConnection extends Command
{
    protected $signature = 'payment:test-connection';
    protected $description = 'Test connection to Payment Gateway SOAP service';

    public function handle()
    {
        try {
            $this->info('Testing connection to Payment Gateway SOAP...');

            $wsdl = config('payment.wsdl');
            $this->line("WSDL: " . $wsdl);

            // Configuración del cliente SOAP
            $context = stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ]);

            $client = new SoapClient($wsdl, [
                'trace' => 1,
                'exceptions' => true,
                'stream_context' => $context,
                'connection_timeout' => 30
            ]);

            $this->info('✅ SOAP Client initialized successfully');

            // Probamos el método Ping
            $this->line('Testing Ping method...');
            $pingResult = $client->Ping();
            
            // Manejar la respuesta (puede ser un objeto)
            if (is_object($pingResult)) {
                $this->info('✅ Ping successful - Response is an object');
                $this->line('Response type: ' . get_class($pingResult));
                
                // Convertir a array para inspeccionar
                $responseArray = (array)$pingResult;
                $this->line('Response content: ' . json_encode($responseArray));
                
                // Si tiene una propiedad específica (común en SOAP)
                if (isset($pingResult->PingResult)) {
                    $this->info('Ping result: ' . $pingResult->PingResult);
                }
            } else {
                $this->info('✅ Ping successful: ' . $pingResult);
            }

            // Listar métodos disponibles
            $this->line('Available methods:');
            $methods = $client->__getFunctions();
            foreach ($methods as $method) {
                $this->line(" - " . $method);
            }

            // Mostrar tipos y estructuras
            $this->line('Available types:');
            $types = $client->__getTypes();
            foreach ($types as $type) {
                $this->line(" - " . $type);
            }

        // ←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←
        // AQUÍ VA EL NUEVO CÓDIGO PARA PROBAR EL MÉTODO SALE
        // ←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←

        $this->line('Testing Sale method with test data...');

        try {
            $saleParams = [
                'APIKey' => config('payment.api_key'),
                'accountToken' => 'ff60a300-a475-41ce-8a28-6f11a8a19592', // Tu token
                'accessCode' => config('payment.access_code', 'TEST_ACCESS'), // Usar valor por defecto
                'merchantAccountNumber' => config('payment.merchant_id'),
                'terminalName' => config('payment.terminal_id'),
                'clientTracking' => 'TEST_' . uniqid(),
                'amount' => 1.00,
                'currencyCode' => 'USD',
                'emailAddress' => 'test@example.com',
                'billingAddress' => 'Test Address',
                'billingCity' => 'Panama City',
                'billingState' => 'PA',
                'billingCountry' => 'PA',
                'billingZipCode' => '0801',
                'billingPhoneNumber' => '600-000-0000',
                'systemTracking' => 'SYS_' . uniqid()
            ];

            $saleResult = $client->Sale($saleParams);
            $this->info('✅ Sale method executed');
            $this->line('Sale response: ' . json_encode($saleResult));

        } catch (\SoapFault $e) {
            $this->error('❌ Sale Error: ' . $e->getMessage());
            $this->line('Last request: ' . $client->__getLastRequest());
        }

        // ←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←
        // FIN DEL NUEVO CÓDIGO
        // ←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←←





            $this->info('🎉 Connection test completed successfully!');

        } catch (\SoapFault $e) {
            $this->error('❌ SOAP Error: ' . $e->getMessage());
            $this->line('Details: ' . $e->getFile() . ':' . $e->getLine());
            
            if (isset($client)) {
                $this->line('Last request: ' . $client->__getLastRequest());
                $this->line('Last response: ' . $client->__getLastResponse());
            }
        } catch (\Exception $e) {
            $this->error('❌ General Error: ' . $e->getMessage());
        }
    }

    private function sanitizeParams($params)
{
    $sensitive = ['APIKey', 'accessCode', 'accountToken', 'cvv'];
    foreach ($sensitive as $key) {
        if (isset($params[$key])) {
            $params[$key] = '***REDACTED***';
        }
    }
    return $params;
}
}