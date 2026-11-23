<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory,SoftDeletes;

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT='deletedAt';

    protected $fillable = [
        'orderId', 'productId', 'quantity', 'productPrice', 'productImage','productName', 'productDescription'
        ,'productSalePrice'
    ];

    /**
     * appends extra column with retrived data
     *
     * @var array
     */
    protected $appends = ['productImageFullUrl'];

    public function getDateFormat()
    {
        return 'U';
    }

    /**
     * getproductImageFullUrlAttribute : return full url of productImage
     *
     * @return void
     */
    public function getproductImageFullUrlAttribute()
    {
        return is_null($this->attributes['productImage']) ? asset('img/default-avatar.svg')
         : Storage::disk('s3')->url($this->attributes['productImage']);
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'orderId', 'id');
    }
    public function product(){
        return $this->belongsTo(Product::class,'productId','id');
    }

}
