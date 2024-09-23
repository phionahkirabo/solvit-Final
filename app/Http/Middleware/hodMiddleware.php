<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Closure;

use Illuminate\Http\Request;

class hodMiddleware
{
    public function handle(Request $request, Closure $next, $guard = null)
    {
        if (Auth::guard('hod')->guest()) {
            // Check if it's an API request
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            // Otherwise, redirect to the HOD login page
            return response()->json(['message' => 'You should login again'], 401); // You may adjust the login route if necessary
        }

        return $next($request);
    }
}
