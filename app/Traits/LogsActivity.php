<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait LogsActivity
{
    /**
     * Log an activity for the currently authenticated user.
     *
     * @param string $action       e.g. 'ticket_add', 'status_change'
     * @param string $description  Human-readable description
     */
    protected function logActivity(string $action, string $description = ''): void
    {
        $user = Auth::user();

        ActivityLog::create([
            'user_id'     => $user?->id,
            'user_name'   => $user ? ($user->firstName . ' ' . $user->lastName) : 'System',
            'action'      => $action,
            'description' => $description,
            'ip_address'  => Request::ip(),
        ]);
    }
}
