<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'passenger_name', 'phone_number', 'priority', 'status', 'description',
        'visa_price', 'payment_received'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}