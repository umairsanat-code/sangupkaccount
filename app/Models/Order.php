<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory,SoftDeletes;

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT='deletedAt';

    protected $fillable = [
        'sellerId', 'buyerId', 'cancelReason', 'status', 'firebaseMessageId', 'address', 'latitude',
        'longitude', 'totalAmount', 'eloumoCharges', 'sellerRating', 'buyerRating', 'sellerFeedback',
        'buyerFeedback', 'paymentMethod', 'transactionId', 'cancelBy', 'paidAmount',
        'isSellerRatingCompleted','isBuyerRatingCompleted','discountAmount','deliveryPerson','deliveryType'
    ];
    protected $appends = ['orderDate','orderTicket','statusHtml'];

    public function getorderDateAttribute()
    {
        return date('d-m-Y',$this->attributes['createdAt']);
    }
    public function getorderTicketAttribute()
    {
        return sprintf("%05d", $this->attributes['id']);
    }

    /**
     * getstatusHtmlAttribute : return success or danger label based on status
     *
     * @return string
     */
    public function getstatusHtmlAttribute():string
    {
        $status=$this->attributes['status'];
        if($status !=OrderStatus::CANCELED){
            return '<button class="btn badge badge-success">'.ucfirst($status).'</button>';
        }else{
            return '<button class="btn badge badge-danger">'.ucfirst($status).'</button>';
        }
    }


    public function getDateFormat()
    {
        return 'U';
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'orderId', 'id');
    }
    public function orderSingleItem()
    {
        return $this->hasOne(OrderItem::class, 'orderId', 'id');
    }
    public function seller()
    {
        return $this->belongsTo(User::class, 'sellerId');
    }
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyerId');
    }
    public function payment()
    {
        return $this->hasMany(Payment::class, 'orderId', 'id');
    }



}
