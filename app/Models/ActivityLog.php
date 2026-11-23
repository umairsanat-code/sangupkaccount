<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'action',
        'description',
        'ip_address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Action badge color helper
    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            'login'         => 'success',
            'logout'        => 'secondary',
            'ticket_add'    => 'primary',
            'status_change' => 'warning',
            'ticket_delete' => 'danger',
            default         => 'info',
        };
    }
}
