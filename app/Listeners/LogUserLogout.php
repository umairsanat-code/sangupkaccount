<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use Illuminate\Auth\Events\Logout;

class LogUserLogout
{
    public function handle(Logout $event): void
    {
        $user = $event->user;

        if (!$user) return;

        ActivityLog::create([
            'user_id'     => $user->id,
            'user_name'   => ($user->firstName ?? '') . ' ' . ($user->lastName ?? ''),
            'action'      => 'logout',
            'description' => 'User logged out.',
            'ip_address'  => request()->ip(),
        ]);
    }
}
