<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifiedSellerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(Auth::user()->hasRole('seller')){
            return $next($request);
        }
        return response()->json(['response' => ['status' => false, 'message' => "Sorry! Yon can't access product because you are not seller"]], JsonResponse::HTTP_FORBIDDEN);

    }
}
