<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerFile extends Model
{
    use HasFactory;
    protected $fillable = ['customer_id','file_path','file_type'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
