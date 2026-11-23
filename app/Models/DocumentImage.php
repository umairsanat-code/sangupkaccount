<?php

namespace App\Models;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DocumentImage extends Model
{
    use HasFactory;

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';
    protected $fillable = ['documentId', 'documentUrl'];
    protected $appends = ['fullDocumentUrl'];

    public function getDateFormat()
    {
        return 'U';
    }


    /**
     * getfullDocumentUrlAttribute : return image full url acccording to env
     *
     * @return void
     */
    public function getfullDocumentUrlAttribute()
    {

        return $this->attributes['documentUrl'] ? Storage::disk('s3')->url($this->attributes['documentUrl']):'';
    }
}
