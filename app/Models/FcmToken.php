<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FcmToken extends Model
{
    use HasFactory;

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';


    protected $fillable = [
        'token', 'deviceidentifier', 'userId','isActive'
    ];

    public function getDateFormat()
    {
        return 'U';
    }
}
