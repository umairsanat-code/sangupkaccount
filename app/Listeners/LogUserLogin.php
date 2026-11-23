<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use Illuminate\Auth\Events\Login;

class LogUserLogin
{
    public function handle(Login $event): void
    {
        $user = $event->user;

            ActivityLog::create([
            'user_id'     => $user->id,
            'user_name'   => ($user->firstName ?? '') . ' ' . ($user->lastName ?? ''),
            'action'      => 'login',
            'description' => 'User logged in.',
            'ip_address'  => request()->ip(),
        ]);
    }
}
