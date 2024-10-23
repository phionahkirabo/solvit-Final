<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthHodOrEmployee
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
        // Check if the user is authenticated as an HOD
        if (auth('hod')->check()) {
            return $next($request); // Allow access if HOD is authenticated
        }

        // Check if the user is authenticated as an Employee
        if (auth('employee')->check()) {
            return $next($request); // Allow access if Employee is authenticated
        }

        // If neither are authenticated, deny access
        return response()->json(['error' => 'Unauthorized'], 401);
    }

}
