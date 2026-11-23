<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisaUmrahPayment extends Model
{
    use HasFactory;

    public const METHODS = [
        'cash',
        'bank',
        'card',
        'online',
    ];

    protected $fillable = [
        'booking_id',
        'service_id',
        'party',
        'payment_method',
        'amount',
        'payment_date',
        'reference',
        'remarks',
        'created_by',
    ];

    public function booking()
    {
        return $this->belongsTo(VisaUmrahBooking::class, 'booking_id');
    }

    public function service()
    {
        return $this->belongsTo(VisaUmrahService::class, 'service_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}