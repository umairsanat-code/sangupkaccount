<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profit extends Model
{
    use HasFactory;
    protected $fillable = [
        'sale_id',
        'profit_amount'
    ];

    // Relationship with Sale model
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
