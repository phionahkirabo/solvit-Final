<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class employeesMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {
        // Check if the employee is not authenticated
        if (Auth::guard('employee')->guest()) {
            // If the request expects JSON or is an AJAX request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Unauthorized'], 401);
            } else {
                // If it's a normal request, redirect to the login page
                return redirect(url('api/login'));
            }
        }

        // Continue with the request if authenticated
        return $next($request);
    }
}
