<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTicket extends Model
{
    use HasFactory;
    protected $fillable = [
        'sale_id',
        'amount_paid',
        'payment_method',
        'bank_name',
        'card_number',
        'payment_date',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
