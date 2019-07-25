<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminUserCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::user()->user_type != 'admin') {
            $response = api_create_response(2, config('api.FAILURE_TEXT'), 'Invalid Request.');
            return response()->json($response, config('api.status_codes')->bad_request);
        }

        return $next($request);
    }
}
