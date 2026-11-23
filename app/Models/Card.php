<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Card extends Model
{
    use HasFactory;

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';


    protected $fillable = [
        'userId', 'lastDigits', 'cardId', 'cardHolderName', 'cardBrandUrl', 'cardBrandName', 'isDefault'
    ];

    public function getDateFormat()
    {
        return 'U';
    }
}
