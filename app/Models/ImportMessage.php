<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportMessage extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'ticket_id', 'message', 'created_by', 'received_by', 'is_read'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
