<?php

// config/payment.php
// return [
//     'api_key' => env('PAYMENT_API_KEY', 'CzsdqL34X0Ii'),
//     'access_code' => env('PAYMENT_ACCESS_CODE', ''), // ← NUEVO
//     'merchant_id' => env('PAYMENT_MERCHANT_ID', '111338'),
//     'terminal_id' => env('PAYMENT_TERMINAL_ID', '111338001'),
//     'wsdl' => env('PAYMENT_WSDL', 'https://tokenv2.test.merchantprocess.net/TokenWebService.asmx?wsdl'),
//     'widget_url_test' => env('PAYMENT_WIDGET_URL_TEST', 'https://apicomponentv2-test.merchantprocess.net/UIComponent/CreditCard'),
//     'widget_url_prod' => env('PAYMENT_WIDGET_URL_PROD', 'https://gateway.merchantprocess.net/securecomponent/v2/UIComponent/CreditCard'),
//     'test_mode' => env('PAYMENT_TEST_MODE', true),
//     'currency' => env('PAYMENT_DEFAULT_CURRENCY', 'USD'),
// ];

return [
    'api_key' => env('PAYMENT_API_KEY', 'CzsdqL34X0Ii'),
    'access_code' => env('PAYMENT_ACCESS_CODE', ''), // ← NUEVO, necesario para SOAP
    'merchant_id' => env('PAYMENT_MERCHANT_ID', '111338'),
    'terminal_id' => env('PAYMENT_TERMINAL_ID', '111338001'),
    'wsdl' => env('PAYMENT_WSDL', 'https://tokenv2.test.merchantprocess.net/TokenWebService.asmx?wsdl'),
    'test_mode' => env('PAYMENT_TEST_MODE', true),
    'currency' => env('PAYMENT_DEFAULT_CURRENCY', 'USD'),
];

