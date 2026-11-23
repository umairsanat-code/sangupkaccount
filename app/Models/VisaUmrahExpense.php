<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisaUmrahExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'description',
        'amount',
        'expense_date',
        'created_by',
    ];

    public function booking()
    {
        return $this->belongsTo(VisaUmrahBooking::class, 'booking_id');
    }
}