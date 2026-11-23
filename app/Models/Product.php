<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory,SoftDeletes;

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT='deletedAt';

    protected $fillable = [
        'sellerId', 'categoryId', 'subCategoryId', 'name', 'productPrice', 'salePrice','inStockQuantity',
        'address', 'latitude', 'longitude', 'description','isProductonSale','isSellerBlocked','saleDiscountPercentage',
        'videoUrl','productCondition'
    ];

    public function getDateFormat()
    {
        return 'U';
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'sellerId', 'id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'categoryId', 'id');
    }
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'subCategoryId', 'id');
    }
    public function imageProducts(){
        return $this->hasMany(ProductImage::class,'productId','id');
    }
    public function productOneImage(){
        return $this->hasOne(ProductImage::class,'productId','id');
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class,'productId','id');
    }
    public function favouriteProduct()
    {
        return $this->hasMany(FavouriteProduct::class,'productId','id');
    }
    public function cart(){
        return $this->hasMany(Cart::class,'productId','id');
    }
    public function getViewers()
    {
        return $this->hasMany(ProductViewer::class, 'productId');
    }




}
