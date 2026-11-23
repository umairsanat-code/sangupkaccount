<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory,SoftDeletes;

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT='deletedAt';

    protected $fillable = [
        'orderId', 'userId', 'paymentType','clearedAmount','earnedAmount','refundedAmount'
    ];
    protected $appends = ['paymentDate'];

    public function getpaymentDateAttribute()
    {
        return date('d-m-Y',$this->attributes['createdAt']);
    }

    public function getDateFormat()
    {
        return 'U';
    }
    public function order()
    {
        return $this->belongsTo(Order::class, 'orderId', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'userId', 'id');
    }
}
