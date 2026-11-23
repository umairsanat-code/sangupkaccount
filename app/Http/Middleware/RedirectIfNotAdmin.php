<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RedirectIfNotAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        Log::info('Middleware Check', [
            'url' => $request->fullUrl(),
            'auth_check' => auth()->check(),
            'user_id' => auth()->id(),
            'has_admin_role' => auth()->check() ? auth()->user()->hasRole('admin') : 'not logged in',
        ]);
        if (auth()->check() && !auth()->user()->hasRole('admin')) {
//            return redirect()->away('https://sangupk.com/daily_report');
            return redirect()->away('http://testing_project.test/daily_report');
        }

        return $next($request);
    }
}
