<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FavouriteProduct extends Model
{
    use HasFactory;

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';


    protected $fillable = [
        'productId', 'userId'
    ];

    public function getDateFormat()
    {
        return 'U';
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'productId', 'id');
    }
}
