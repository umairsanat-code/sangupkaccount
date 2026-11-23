<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $fillable=[
      'user_id','client_id','is_read','message','status','message_date','message_time','phoneNumber'
    ];
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

}
