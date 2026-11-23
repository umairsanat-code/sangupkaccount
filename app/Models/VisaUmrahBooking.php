<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisaUmrahBooking extends Model
{
    use HasFactory;

    public const VISA_STATUSES = [
        'pending',
        'documents_required',
        'processing',
        'approved',
        'rejected',
        'completed',
        'cancelled',
    ];

    public const BOOKING_STATUSES = [
        'new',
        'confirmed',
        'active',
        'completed',
        'cancelled',
    ];

    protected $fillable = [
        'booking_type',
        'booking_no',
        'client_name',
        'client_phone',
        'client_email',
        'passport_number',
        'nationality',
        'visa_country',
        'visa_type',
        'num_travelers',
        'travel_date',
        'return_date',
        'visa_status',
        'num_adults',
        'num_children',
        'umrah_package',
        'booking_status',
        'main_agency_id',
        'main_agency_reference',
        'main_agency_cost',
        'main_client_total',
        'client_total',
        'client_received',
        'agency_total',
        'agency_paid',
        'additional_expenses',
        'notes',
        'created_by',
    ];

    public function mainAgency()
    {
        return $this->belongsTo(Agency::class, 'main_agency_id');
    }

    public function services()
    {
        return $this->hasMany(VisaUmrahService::class, 'booking_id');
    }

    public function payments()
    {
        return $this->hasMany(VisaUmrahPayment::class, 'booking_id');
    }

    public function clientPayments()
    {
        return $this->hasMany(VisaUmrahPayment::class, 'booking_id')
            ->where('party', 'client');
    }

    public function agencyPayments()
    {
        return $this->hasMany(VisaUmrahPayment::class, 'booking_id')
            ->where('party', 'agency');
    }

    public function documents()
    {
        return $this->hasMany(VisaUmrahDocument::class, 'booking_id');
    }

    public function expenses()
    {
        return $this->hasMany(VisaUmrahExpense::class, 'booking_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Financial helpers (always computed from live rows)
    |--------------------------------------------------------------------------
    */

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

    public function servicesClientTotal()
    {
        return (float) $this->services()->sum('client_price');
    }

    public function servicesAgencyTotal()
    {
        return (float) $this->services()->sum('agency_cost');
    }

    public function expensesTotal()
    {
        return (float) $this->expenses()->sum('amount');
    }

    public function clientTotalFull()
    {
        return (float) $this->main_client_total + $this->servicesClientTotal();
    }

    public function agencyTotalFull()
    {
        return (float) $this->main_agency_cost + $this->servicesAgencyTotal();
    }

    public function clientPending()
    {
        return $this->clientTotalFull() - $this->clientReceivedTotal();
    }

    public function agencyPending()
    {
        return $this->agencyTotalFull() - $this->agencyPaidTotal();
    }

    public function grossProfit()
    {
        return $this->clientTotalFull() - $this->agencyTotalFull();
    }

    public function additionalExpensesTotal()
    {
        return $this->expensesTotal();
    }

    public function netProfit()
    {
        return $this->grossProfit() - $this->additionalExpensesTotal();
    }

    public function receivedProfit()
    {
        return $this->clientReceivedTotal() - $this->agencyPaidTotal() - $this->additionalExpensesTotal();
    }

    public function isVisa()
    {
        return $this->booking_type === 'visa';
    }

    public function isUmrah()
    {
        return $this->booking_type === 'umrah';
    }
}