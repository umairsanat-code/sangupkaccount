<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DailySaleTicket extends Model
{
    use HasFactory;
    protected $fillable = [
        'date', 'pnr', 'system', 'issued_price', 'time_limit', 'pax_name',
        'payment', 'payment_method', 'pendings', 'sale_price', 'remarks','ticket_number','user_id','type','status'
    ];
    protected $casts = [
    'time_limit' => 'datetime', // <-- ensures Carbon instance
    'date' => 'date',
];
public function getTimeLimitAttribute($value)
{
    return Carbon::parse($value)->timezone('Europe/Madrid'); // always show in Spain time
}

public function setTimeLimitAttribute($value)
{
    $this->attributes['time_limit'] = Carbon::parse($value, 'Europe/Madrid')->timezone('UTC');
    // always save in UTC
}
    public function payments()
    {
        return $this->hasMany(DailySaleTicketPayment::class);
    }

}
