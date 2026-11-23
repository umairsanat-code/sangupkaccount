<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $fillable=[
      'name','email','phone','pnr','total_customer','service-detail','service_number','file','id_card','customer_pnr','passport_no'
    ];
protected static function boot()
{
    parent::boot();

    static::creating(function ($customer) {
        // Prefix based on current year and month (e.g., 202508)
        $prefix = date('Ym');

        // Get the last service_number that starts with this prefix
        $lastCustomer = self::where('service_number', 'like', $prefix . '%')
                            ->orderBy('service_number', 'desc')
                            ->first();

        if ($lastCustomer) {
            // Get last 4 digits and increment
            $lastNumber = (int)substr($lastCustomer->service_number, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            // Start from 0001
            $nextNumber = 1;
        }

        // Pad the number to 4 digits and build full service number
        $customer->service_number = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    });
}

    // protected static function boot()
    // {
    //     parent::boot();

    //     // Generate a unique service number before creating a record
    //     static::creating(function ($customer) {
    //         do {
    //             $serviceNumber = rand(100000, 9999999999);; // Generate a random number (6 to 10 digits)
    //         } while (self::where('service_number', $serviceNumber)->exists());

    //         $customer->service_number = $serviceNumber; // Assign the unique service number
    //     });
    // }
    public function tickets(){
        return $this->hasMany(Ticket::class,'customer_id');
    }

}
