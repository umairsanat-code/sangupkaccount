<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    // Define the fillable properties for mass-assignment
    protected $fillable = [
        'ticket_number',
        'airline_id',
        'customer_id',
        'flight_date',
        'booking_date',
        'departure_city',
        'destination_city',
        'class',
        'status',
        'refund_status',
        'purchase_price','remaining_amount','agent_id','customer_name','author_name','pnr','note','passport_no','id_card'
    ];

    // Relationship with Customer (Ticket belongs to one Customer)
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    // Relationship with Airline (Ticket belongs to one Airline)
    public function airline()
    {
        return $this->belongsTo(Airline::class);
    }
    public function sales()
    {
        return $this->hasMany(Sale::class, 'ticket_id');
    }

}
