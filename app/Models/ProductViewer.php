<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductViewer extends Model
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
    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'productId');
    }
}
