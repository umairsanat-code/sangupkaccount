<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisaUmrahService extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'service_type',
        'name',
        'city',
        'country',
        'check_in',
        'check_out',
        'num_nights',
        'room_type',
        'num_rooms',
        'num_guests',
        'meal_plan',
        'pickup_location',
        'dropoff_location',
        'pickup_date',
        'pickup_time',
        'vehicle_type',
        'num_passengers',
        'agency_id',
        'agency_reference',
        'agency_cost',
        'client_price',
    ];

    public function booking()
    {
        return $this->belongsTo(VisaUmrahBooking::class, 'booking_id');
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id');
    }

    public function payments()
    {
        return $this->hasMany(VisaUmrahPayment::class, 'service_id')
            ->whereNotNull('service_id');
    }

    public function clientReceivedTotal()
    {
        return (float) $this->payments()
            ->where('party', 'client')
            ->sum('amount');
    }

    public function agencyPaidTotal()
    {
        return (float) $this->payments()
            ->where('party', 'agency')
            ->sum('amount');
    }

    public function clientPending()
    {
        return (float) $this->client_price - $this->clientReceivedTotal();
    }

    public function agencyPending()
    {
        return (float) $this->agency_cost - $this->agencyPaidTotal();
    }

    public function profit()
    {
        return (float) $this->client_price - $this->agency_cost;
    }

    public function isHotel()
    {
        return $this->service_type === 'hotel';
    }

    public function isTransport()
    {
        return $this->service_type === 'transport';
    }
}