<?php

// namespace App\Notifications;

// use App\Models\PaymentTransaction;
// use Illuminate\Bus\Queueable;
// use Illuminate\Contracts\Queue\ShouldQueue; // opcional si quieres encolar
// use Illuminate\Mail\Mailable;
// use Illuminate\Queue\SerializesModels;

// class PaymentReceiptMail extends Mailable
// {
//     public $transaction;

//     public function __construct($transaction)
//     {
//         $this->transaction = $transaction;
//     }

//     public function build()
//     {
//         $t = $this->transaction;

//         // Mapea 840 → USD (si ya guardas el código ISO numérico)
//         $currency = $t->currency ?? 'USD';
//         $currencyLabel = ($currency === '840' || strtoupper($currency) === 'USD') ? 'USD' : $currency;

//         // Datos útiles que seguramente ya tienes
//         $fullName = data_get($t, 'request_data.full_name') 
//                  ?? data_get($t, 'token.cardholder_name') 
//                  ?? 'Cliente';
//         $email    = data_get($t, 'request_data.email');
//         $ruex     = data_get($t, 'request_data.ruex');
//         $last4    = data_get($t, 'token.last_four_digits', '****');
//         $brand    = data_get($t, 'token.brand');

//         // URL absoluta del logo (APP_URL debe estar configurado)
//         $logoUrl = asset('images/LOGOconBorde200x229.png');

//         // Enlace al comprobante (opcional: a tu pantalla de success con id)
//         $receiptUrl = route('payment.success', ['transaction' => $t->id]);

//         return $this->subject('Comprobante de pago')
//             ->markdown('mails.payment-receipt', [
//                 't'             => $t,
//                 'fullName'      => $fullName,
//                 'email'         => $email,
//                 'ruex'          => $ruex,
//                 'last4'         => $last4,
//                 'brand'         => $brand,
//                 'currencyLabel' => $currencyLabel,
//                 'logoUrl'       => $logoUrl,
//                 'receiptUrl'    => $receiptUrl,
//             ]);
//     }
// }

namespace App\Notifications;

use App\Models\PaymentTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public $transaction;

    public function __construct($transaction)
    {
        $this->transaction = $transaction;
    }

    public function build()
    {
        $t = $this->transaction;

        // Mapea 840 → USD (si ya guardas el código ISO numérico)
        $currency = $t->currency ?? 'USD';
        $currencyLabel = ($currency === '840' || strtoupper($currency) === 'USD') ? 'USD' : $currency;

        // Datos útiles que seguramente ya tienes
        $fullName = data_get($t, 'request_data.full_name') 
                 ?? data_get($t, 'token.cardholder_name') 
                 ?? 'Cliente';
        $email    = data_get($t, 'request_data.email');
        $ruex     = data_get($t, 'request_data.ruex');
        $last4    = data_get($t, 'token.last_four_digits', '****');
        $brand    = data_get($t, 'token.brand');

        // URL absoluta del logo (APP_URL debe estar configurado)
        $logoUrl = asset('images/LOGOconBorde200x229.png');

        // Enlace al comprobante (opcional: a tu pantalla de success con id)
        $receiptUrl = route('payment.success', ['transaction' => $t->id]);

        return $this->subject('Comprobante de pago')
            ->markdown('mails.payment-receipt', [
                't'             => $t,
                'fullName'      => $fullName,
                'email'         => $email,
                'ruex'          => $ruex,
                'last4'         => $last4,
                'brand'         => $brand,
                'currencyLabel' => $currencyLabel,
                'logoUrl'       => $logoUrl,
                'receiptUrl'    => $receiptUrl,
            ]);
    }
}