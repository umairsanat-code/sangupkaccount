<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;
    // Define fillable attributes
    protected $fillable = [
        'ticket_id',
        'sale_price',
        'sale_date',
        'agent_id','service_charges','airline_charges',
        'payment_status','paid_amount','total_amount_paid','pending_amount','refund_amount','refund_reason'
    ];

    // Relationship with Ticket model
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    // Relationship with Profit model
    public function profit()
    {
        return $this->hasOne(Profit::class);
    }
    public function paymentMethod()
    {
        return $this->hasOne(PaymentTicket::class);
    }
    public function payments()
    {
        return $this->hasMany(PaymentTicket::class, 'sale_id');
    }

}
