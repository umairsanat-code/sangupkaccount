<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisaUmrahDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'label',
        'file_path',
        'created_by',
    ];

    public function booking()
    {
        return $this->belongsTo(VisaUmrahBooking::class, 'booking_id');
    }
}