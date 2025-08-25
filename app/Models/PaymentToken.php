<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'token', 
        'account_number',       
        'last_four_digits', 
        'brand', 
        'cardholder_name',      
        'expiry_date', 
        'is_default', 
        'is_active'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }
}