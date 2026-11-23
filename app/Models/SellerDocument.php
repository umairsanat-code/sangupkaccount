<?php

namespace App\Models;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SellerDocument extends Model
{
    use HasFactory, SoftDeletes;

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';


    protected $fillable = [
        'userId', 'status','rejectedReason','latitude','longitude','address'
    ];
    protected $with=['userDocuments'];
    public function getDateFormat()
    {
        return 'U';
    }

    public function userDocuments()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function images()
    {
        return $this->hasMany(DocumentImage::class, 'documentId');
    }


}
