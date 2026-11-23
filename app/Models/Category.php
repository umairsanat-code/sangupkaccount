<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory,SoftDeletes;

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT='deletedAt';

    protected $fillable = [
        'name', 'imageUrl'
    ];

    public function getDateFormat()
    {
        return 'U';
    }
    public function subCategories()
    {
        return $this->hasMany('App\Models\SubCategory', 'categoryId');
    }
    public function products(){
        return $this->hasMany(Product::class,'categoryId');
    }
}
