<?php
// app/Models/PaymentEmailLog.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentEmailLog extends Model
{
    protected $fillable = [
        'payment_transaction_id',
        'to_email',
        'subject',
        'was_sent',
        'error_message',
        'provider_message_id',
        'sent_at',
    ];

    protected $casts = [
        'was_sent' => 'boolean',
        'sent_at'  => 'datetime',
    ];

    public function transaction() {
        return $this->belongsTo(PaymentTransaction::class, 'payment_transaction_id');
    }
}
