<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailySaleTicketPayment extends Model
{
    use HasFactory;
    protected $fillable = [
        'daily_sale_ticket_id',
        'amount',
        'payment_date',
        'payment_method',
        'remarks'
    ];

    public function ticket()
    {
        return $this->belongsTo(DailySaleTicket::class);
    }
}
