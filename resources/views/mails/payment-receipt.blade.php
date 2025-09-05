@component('mail::message')
{{-- Logo centrado (usa URL absoluta para que cargue en clientes de correo) --}}
<p align="center" style="margin:0 0 12px;">
  <img src="{{ $logoUrl }}" alt="Servicio Nacional de Migración" width="120" style="max-width:100%; border:0;">
</p>

# Pago confirmado

Hola **{{ $fullName }}**, tu pago se ha procesado con éxito.

@component('mail::panel')
**Monto pagado:** {{ number_format((float) ($t->amount ?? 0), 2) }} {{ $currencyLabel }}
@endcomponent

@component('mail::table')
| Dato | Valor |
|:-----|:------|
| **Fecha** | {{ optional($t->created_at)->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }} |
| **Referencia** | {{ $t->reference ?? '—' }} |
| **Transacción** | {{ $t->gateway_transaction_id ?? '—' }} |
| **Autorización** | {{ $t->authorization_number ?? '—' }} |
| **Tarjeta** | **** {{ $last4 }} {{ $brand ? '(' . $brand . ')' : '' }} |
| **RUEX** | {{ $ruex ?? '—' }} |
| **Correo** | {{ $email ?? '—' }} |
@endcomponent

@component('mail::button', ['url' => $receiptUrl])
Ver comprobante
@endcomponent

Si no reconoces este pago, por favor contacta a tu banco.

Gracias,<br>
**Servicio Nacional de Migración**
@endcomponent
