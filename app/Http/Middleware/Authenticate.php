<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;
use Closure;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */

    //  CA
    // protected function redirectTo($request)
    // {
    //     
    //     if (! $request->expectsJson()) {
    //         return route('login');
    //     }
    // }

    // CA
    public function handle($request, Closure $next) {
        if(Auth::check()) {
            return $next($request);
        } else {
            return response()->json([
                'code' => 401,
                'status' => false,
                'message' => 'Not authorized'
            ], 401);
        }
    }

}
