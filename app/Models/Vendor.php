<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;
    protected $fillable = [
        'agent_id',
        'ticket_id',
        'payment_amount',
        'payment_method',
        'start_date',
        'end_date','remaining_amount'
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
