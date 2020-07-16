<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
class AuthenticationApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if( !Auth::check() ) {
            return response()->json(['error' => __('Unauthorized')], 401);
        }
        return $next($request);
    }
}
