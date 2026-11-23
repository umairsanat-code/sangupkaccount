<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_person',
        'phone',
        'whatsapp',
        'email',
        'country',
        'city',
        'address',
        'services',
        'created_by',
    ];

    protected $casts = [
        'services' => 'array',
    ];

    public function mainBookings()
    {
        return $this->hasMany(VisaUmrahBooking::class, 'main_agency_id');
    }

    public function services()
    {
        return $this->hasMany(VisaUmrahService::class, 'agency_id');
    }
}