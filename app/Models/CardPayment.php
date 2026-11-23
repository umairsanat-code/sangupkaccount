<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardPayment extends Model
{
    use HasFactory;

    public const BANK_NAMES = [
        'SANTANDER SANGU',
        'BBVA SANGU',
        'LACIAXA SANGU',
        'BBVA IMRO',
        'LACIAXA IMRO',
    ];

    protected $fillable = [
        'user_id',
        'bank_name',
        'card_payment',
        'received_amount',
        'received_date',
        'sent_amount',
        'sent_date',
    ];

    protected $casts = [
        'received_amount' => 'decimal:2',
        'sent_amount' => 'decimal:2',
        'received_date' => 'date',
        'sent_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
